import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  Alert,
  ActivityIndicator,
  Modal,
  Image,
} from 'react-native';
import QRCode from 'react-native-qrcode-svg';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';

export default function SellScreen() {
  const [phone, setPhone] = useState('');
  const [amountSats, setAmountSats] = useState('');
  const [conversionRate, setConversionRate] = useState(0.023);
  const [loading, setLoading] = useState(false);
  const [calculations, setCalculations] = useState(null);
  const [invoiceModal, setInvoiceModal] = useState(false);
  const [invoiceData, setInvoiceData] = useState(null);

  useEffect(() => {
    fetchExchangeRates();
  }, []);

  const fetchExchangeRates = async () => {
    try {
      const result = await exchangeService.getExchangeRates();
      if (result.status === 'success' && result.btc_zmw) {
        setConversionRate(result.btc_zmw / 100000000);
      }
    } catch (error) {
      console.error('Error fetching rates:', error);
    }
  };

  const calculateSell = () => {
    const amount = parseFloat(amountSats) || 0;
    if (amount < 500) {
      setCalculations(null);
      return;
    }

    const serviceFeeRate = 0.15; // 15%
    const networkFee = 400; // SATS

    const amountBtc = amount / 100000000;
    const conversionFee = amount * serviceFeeRate;
    const totalSats = amount + conversionFee + networkFee;
    const receiveSats = amount - conversionFee;
    const receiveKwacha = receiveSats * conversionRate;

    setCalculations({
      amountBtc,
      conversionFee,
      totalSats,
      receiveSats,
      receiveKwacha,
    });
  };

  useEffect(() => {
    calculateSell();
  }, [amountSats, conversionRate]);

  const handleSell = async () => {
    if (!phone || !amountSats || parseFloat(amountSats) < 500) {
      Alert.alert('Validation Error', 'Please enter a valid phone number and amount (minimum 500 SATS)');
      return;
    }

    if (!calculations) {
      Alert.alert('Error', 'Please wait for calculations to complete');
      return;
    }

    setLoading(true);

    try {
      const data = {
        phone,
        amount_sats: parseFloat(amountSats),
        amount_btc: calculations.amountBtc,
        amount_kwacha: calculations.receiveKwacha,
        conversion_fee: Math.round(calculations.conversionFee),
        total_sats: Math.round(calculations.totalSats),
        network_fee: 400,
      };

      const result = await exchangeService.generateInvoice(data);

      if (result.status === 'error') {
        Alert.alert('Error', result.message || 'Failed to generate invoice');
        setLoading(false);
        return;
      }

      if (result.status === 'success') {
        setInvoiceData(result);
        setInvoiceModal(true);
      }
    } catch (error) {
      Alert.alert('Error', error.message || 'An error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.card}>
        <Text style={styles.title}>Sell Bitcoin for Kwacha</Text>
        <Text style={styles.subtitle}>Send Bitcoin, receive Kwacha to your mobile money</Text>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Mobile Money Number</Text>
          <View style={styles.inputWrapper}>
            <TextInput
              style={styles.input}
              placeholder="09XXXXXXXX"
              placeholderTextColor={theme.colors.textGray}
              value={phone}
              onChangeText={setPhone}
              keyboardType="phone-pad"
            />
          </View>
        </View>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Amount in Satoshis</Text>
          <View style={styles.inputWrapper}>
            <TextInput
              style={styles.input}
              placeholder="Enter amount"
              placeholderTextColor={theme.colors.textGray}
              value={amountSats}
              onChangeText={setAmountSats}
              keyboardType="number-pad"
            />
            <Text style={styles.suffix}>SATS</Text>
          </View>
        </View>

        {calculations && (
          <View style={styles.calcBox}>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Amount (SATS):</Text>
              <Text style={styles.calcValue}>{parseFloat(amountSats).toLocaleString()}</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Service Fee:</Text>
              <Text style={styles.calcValue}>{Math.round(calculations.conversionFee).toLocaleString()} SATS</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Network Fee:</Text>
              <Text style={styles.calcValue}>400 SATS</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Total SATS:</Text>
              <Text style={styles.calcValue}>{Math.round(calculations.totalSats).toLocaleString()}</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>You'll Receive:</Text>
              <Text style={styles.calcValue}>{calculations.receiveKwacha.toFixed(2)} ZMW</Text>
            </View>
          </View>
        )}

        <View style={styles.infoBadge}>
          <Text style={styles.infoText}>
            Rate: 1 SAT = {conversionRate.toFixed(4)} ZMW | Min: 500 SATS
          </Text>
        </View>

        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
          onPress={handleSell}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>Generate Lightning Invoice</Text>
          )}
        </TouchableOpacity>
      </View>

      {/* Invoice Modal */}
      <Modal
        visible={invoiceModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setInvoiceModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Invoice Generated!</Text>
            {invoiceData && (
              <>
                <Text style={styles.modalText}>
                  Scan the QR code with your Lightning wallet to pay.
                </Text>
                <View style={styles.qrContainer}>
                  <View style={styles.qrCodeWrapper}>
                    <QRCode
                      value={invoiceData.bolt11 || ''}
                      size={250}
                      color={theme.colors.text}
                      backgroundColor={theme.colors.white}
                      logo={require('../../assets/icon.png')}
                      logoSize={50}
                      logoBackgroundColor={theme.colors.white}
                      logoMargin={5}
                      logoBorderRadius={10}
                    />
                  </View>
                </View>
                <Text style={styles.invoiceText} selectable>
                  {invoiceData.bolt11}
                </Text>
                <TouchableOpacity
                  style={styles.closeButton}
                  onPress={() => setInvoiceModal(false)}
                >
                  <Text style={styles.closeButtonText}>Close</Text>
                </TouchableOpacity>
              </>
            )}
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  content: {
    padding: theme.spacing.md,
  },
  card: {
    backgroundColor: theme.colors.white,
    borderRadius: theme.borderRadius.xl,
    padding: theme.spacing.lg,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  subtitle: {
    fontSize: 14,
    color: theme.colors.textGray,
    marginBottom: theme.spacing.lg,
  },
  formGroup: {
    marginBottom: theme.spacing.md,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: theme.spacing.xs,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    paddingHorizontal: theme.spacing.md,
  },
  input: {
    flex: 1,
    height: 50,
    fontSize: 16,
    color: theme.colors.text,
  },
  suffix: {
    fontSize: 14,
    color: theme.colors.textGray,
    marginLeft: theme.spacing.sm,
  },
  calcBox: {
    backgroundColor: theme.colors.background,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    marginTop: theme.spacing.md,
    marginBottom: theme.spacing.md,
  },
  calcRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: theme.spacing.sm,
  },
  calcLabel: {
    fontSize: 14,
    color: theme.colors.textGray,
  },
  calcValue: {
    fontSize: 14,
    fontWeight: '600',
    color: theme.colors.text,
  },
  infoBadge: {
    backgroundColor: 'rgba(247, 147, 26, 0.1)',
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    marginBottom: theme.spacing.md,
  },
  infoText: {
    fontSize: 12,
    color: theme.colors.primary,
  },
  button: {
    backgroundColor: theme.colors.primary,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    alignItems: 'center',
    marginTop: theme.spacing.md,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonText: {
    color: theme.colors.white,
    fontSize: 16,
    fontWeight: '600',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    backgroundColor: theme.colors.white,
    borderRadius: theme.borderRadius.xl,
    padding: theme.spacing.lg,
    width: '90%',
    maxWidth: 400,
    alignItems: 'center',
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  modalText: {
    fontSize: 14,
    color: theme.colors.textGray,
    textAlign: 'center',
    marginBottom: theme.spacing.lg,
  },
  qrContainer: {
    backgroundColor: theme.colors.white,
    padding: theme.spacing.md,
    borderRadius: theme.borderRadius.md,
    marginBottom: theme.spacing.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  qrCodeWrapper: {
    borderRadius: theme.borderRadius.md, // Square with rounded corners
    overflow: 'hidden',
    padding: theme.spacing.sm,
    backgroundColor: theme.colors.white,
    width: 250,
    height: 250,
    alignItems: 'center',
    justifyContent: 'center',
  },
  invoiceText: {
    fontSize: 10,
    color: theme.colors.text,
    textAlign: 'center',
    marginBottom: theme.spacing.lg,
    padding: theme.spacing.sm,
    backgroundColor: theme.colors.background,
    borderRadius: theme.borderRadius.sm,
    fontFamily: 'monospace',
  },
  closeButton: {
    backgroundColor: theme.colors.primary,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    width: '100%',
    alignItems: 'center',
  },
  closeButtonText: {
    color: theme.colors.white,
    fontSize: 16,
    fontWeight: '600',
  },
});

