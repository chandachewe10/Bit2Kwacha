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
  Linking,
} from 'react-native';
import QRCode from 'react-native-qrcode-svg';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';

export default function AirtimeScreen() {
  const [phone, setPhone] = useState('');
  const [amountKwacha, setAmountKwacha] = useState('');
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

  const AIRTIME_SERVICE_FEE_RATE = 0.02; // 2% service fee

  const calculateAirtime = () => {
    const amount = parseFloat(amountKwacha) || 0;
    if (amount < 5 || amount > 1000) {
      setCalculations(null);
      return;
    }

    const amountSats = amount / conversionRate;
    const serviceFee = amountSats * AIRTIME_SERVICE_FEE_RATE;
    const totalSats = Math.round(amountSats + serviceFee);
    const amountBtc = totalSats / 100000000;

    setCalculations({
      amountSats: Math.round(amountSats),
      serviceFee: Math.round(serviceFee),
      amountBtc,
      totalSats,
      amountKwacha: amount,
    });
  };

  useEffect(() => {
    calculateAirtime();
  }, [amountKwacha, conversionRate]);

  const handlePurchase = async () => {
    const amount = parseFloat(amountKwacha);
    if (!phone || !amount || amount < 5 || amount > 1000) {
      Alert.alert('Validation Error', 'Please enter a valid phone number and amount between 5 and 1,000 ZMW');
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
        amount_kwacha: calculations.amountKwacha,
        amount_sats: calculations.amountSats,
        amount_btc: calculations.amountBtc,
        total_sats: calculations.totalSats,
      };

      const result = await exchangeService.generateAirtimeInvoice(data);

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

  useEffect(() => {
    if (!invoiceModal || !invoiceData?.bolt11) return;

    const bolt11 = invoiceData.bolt11;
    Alert.alert(
      'Open Bitcoin Wallet',
      'Your invoice is ready. Would you like to open it in your Lightning wallet app?',
      [
        { text: 'View QR Code', style: 'cancel' },
        {
          text: 'Open Wallet',
          onPress: async () => {
            try {
              await Linking.openURL(`lightning:${bolt11}`);
            } catch {
              Alert.alert('Error', 'Could not open wallet. Please scan the QR code instead.');
            }
          },
        },
      ]
    );
  }, [invoiceModal, invoiceData]);

  const handleOpenWallet = async () => {
    if (!invoiceData?.bolt11) return;

    try {
      const url = `lightning:${invoiceData.bolt11}`;
      try {
        await Linking.openURL(url);
      } catch (err) {
        const supported = await Linking.canOpenURL(url);
        if (supported) {
          await Linking.openURL(url);
        } else {
          Alert.alert('Error', 'No compatible wallet found. Please copy the invoice and paste it in your wallet.');
        }
      }
    } catch (error) {
      Alert.alert('Error', 'Could not open wallet to pay invoice.');
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.card}>
        <Text style={styles.title}>Buy Airtime with Bitcoin</Text>
        <Text style={styles.subtitle}>Pay with Bitcoin, receive talktime on your phone</Text>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Phone Number</Text>
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
          <Text style={styles.label}>Airtime Amount</Text>
          <View style={styles.inputWrapper}>
            <TextInput
              style={styles.input}
              placeholder="Enter amount"
              placeholderTextColor={theme.colors.textGray}
              value={amountKwacha}
              onChangeText={setAmountKwacha}
              keyboardType="decimal-pad"
            />
            <Text style={styles.suffix}>ZMW</Text>
          </View>
        </View>

        {calculations && (
          <View style={styles.calcBox}>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Airtime Value:</Text>
              <Text style={styles.calcValue}>{calculations.amountKwacha.toFixed(2)} ZMW</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>You'll Pay:</Text>
              <Text style={styles.calcValue}>{calculations.totalSats.toLocaleString()} SATS</Text>
            </View>
          </View>
        )}

        <View style={styles.infoBadge}>
          <Text style={styles.infoText}>
            Rate: 1 SAT = {conversionRate.toFixed(4)} ZMW | Min: 5 ZMW | Max: 1,000 ZMW
          </Text>
        </View>

        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
          onPress={handlePurchase}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>Generate Lightning Invoice</Text>
          )}
        </TouchableOpacity>
      </View>

      <Modal
        visible={invoiceModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setInvoiceModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Airtime Invoice Generated!</Text>
            {invoiceData && (
              <>
                <Text style={styles.modalText}>
                  Scan the QR code to pay. You'll receive {invoiceData.amount_kwacha} ZMW airtime on {phone}.
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
                <TouchableOpacity
                  style={[styles.button, { marginTop: 0, marginBottom: 15 }]}
                  onPress={handleOpenWallet}
                >
                  <Text style={styles.buttonText}>Open in Bitcoin App</Text>
                </TouchableOpacity>
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
    borderRadius: theme.borderRadius.md,
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
