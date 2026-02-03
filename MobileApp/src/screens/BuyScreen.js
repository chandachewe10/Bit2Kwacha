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
  Linking,
} from 'react-native';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';
import { API_BASE_URL } from '../config/api';

export default function BuyScreen() {
  const [phone, setPhone] = useState('');
  const [amountKwacha, setAmountKwacha] = useState('');
  const [conversionRate, setConversionRate] = useState(0.023);
  const [loading, setLoading] = useState(false);
  const [loadingText, setLoadingText] = useState('Proceed to Payment');
  const [calculations, setCalculations] = useState(null);

  useEffect(() => {
    fetchExchangeRates();
  }, []);

  const fetchExchangeRates = async () => {
    try {
      const result = await exchangeService.getExchangeRates();
      if (result.status === 'success' && result.btc_zmw) {
        // 1 SAT = btc_zmw / 100,000,000 ZMW
        setConversionRate(result.btc_zmw / 100000000);
      }
    } catch (error) {
      console.error('Error fetching rates:', error);
    }
  };

  const calculateBuy = () => {
    const amount = parseFloat(amountKwacha) || 0;
    if (amount < 20) {
      setCalculations(null);
      return;
    }

    const serviceFeeRate = 0.15; // 15%
    const networkFee = 5; // ZMW

    const amountSats = amount / conversionRate;
    const amountBtc = amountSats / 100000000;
    const conversionFee = amount * serviceFeeRate;
    const totalAmount = amount + conversionFee + networkFee;

    setCalculations({
      amountSats,
      amountBtc,
      conversionFee,
      totalAmount,
    });
  };

  useEffect(() => {
    calculateBuy();
  }, [amountKwacha, conversionRate]);

  const handleBuy = async () => {
    if (!phone || !amountKwacha || parseFloat(amountKwacha) < 20) {
      Alert.alert('Validation Error', 'Please enter a valid phone number and amount (minimum 20 ZMW)');
      return;
    }

    if (!calculations) {
      Alert.alert('Error', 'Please wait for calculations to complete');
      return;
    }

    setLoading(true);
    setLoadingText('Checking Balance...');

    try {
      // Check balance first
      const balanceCheck = await exchangeService.checkBalance(calculations.amountSats);

      if (balanceCheck.status === 'error') {
        Alert.alert('Balance Check Failed', balanceCheck.message || 'Unable to verify account balance');
        setLoading(false);
        setLoadingText('Proceed to Payment');
        return;
      }

      if (!balanceCheck.sufficient) {
        const balanceSats = Math.round(balanceCheck.balance_sats || 0).toLocaleString();
        const requiredSats = Math.round(balanceCheck.required_sats || 0).toLocaleString();
        Alert.alert(
          'Insufficient Balance',
          `We currently don't have enough Bitcoin in stock. You requested ${requiredSats} SATS, but we only have ${balanceSats} SATS available.`
        );
        setLoading(false);
        setLoadingText('Proceed to Payment');
        return;
      }

      // Balance is sufficient, proceed to payment
      setLoadingText('Proceeding to Payment...');

      // Submit data to subscription/payment route and open in browser
      const paymentUrl = `${API_BASE_URL}/subscription/payment`;

      // Build query parameters
      const params = new URLSearchParams({
        phone: phone,
        amount_kwacha: parseFloat(amountKwacha).toString(),
        amount_sats: Math.round(calculations.amountSats).toString(),
        amount_btc: calculations.amountBtc.toString(),
        conversion_fee: calculations.conversionFee.toString(),
        network_fee: '5',
        total_amount: calculations.totalAmount.toString(),
        type: 'buy',
      });

      const fullUrl = `${paymentUrl}?${params.toString()}`;

      // Open payment page in browser
      const canOpen = await Linking.canOpenURL(fullUrl);
      if (canOpen) {
        await Linking.openURL(fullUrl);
      } else {
        Alert.alert('Error', 'Unable to open payment page. Please check your internet connection.');
      }

    } catch (error) {
      console.error('Payment error:', error);
      Alert.alert('Error', error.message || 'An error occurred. Please try again.');
      setLoadingText('Proceed to Payment');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.card}>
        <Text style={styles.title}>Buy Bitcoin with Kwacha</Text>
        <Text style={styles.subtitle}>Pay with mobile money or card, receive Bitcoin instantly</Text>

        <View style={styles.formGroup}>
          <Text style={styles.label}>Phone number to make the mobile money payments</Text>
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
          <Text style={styles.label}>Amount in Kwacha (ZMW)</Text>
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
              <Text style={styles.calcLabel}>Amount (ZMW):</Text>
              <Text style={styles.calcValue}>{parseFloat(amountKwacha).toFixed(2)}</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Service Fee:</Text>
              <Text style={styles.calcValue}>{calculations.conversionFee.toFixed(2)} ZMW</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Network Fee:</Text>
              <Text style={styles.calcValue}>5.00 ZMW</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Total to Pay:</Text>
              <Text style={styles.calcValue}>{calculations.totalAmount.toFixed(2)} ZMW</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>You'll Receive:</Text>
              <Text style={styles.calcValue}>{calculations.amountBtc.toFixed(8)} BTC</Text>
            </View>
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>SATS:</Text>
              <Text style={styles.calcValue}>{Math.round(calculations.amountSats).toLocaleString()}</Text>
            </View>
          </View>
        )}

        <View style={styles.infoBadge}>
          <Text style={styles.infoText}>
            Rate: 1 ZMW = ~{(1 / conversionRate).toFixed(2)} SATS | Min: 20 ZMW
          </Text>
        </View>

        <TouchableOpacity
          style={[styles.button, loading && styles.buttonDisabled]}
          onPress={handleBuy}
          disabled={loading}
        >
          {loading ? (
            <View style={styles.buttonLoading}>
              <ActivityIndicator color="#fff" size="small" />
              <Text style={styles.buttonText}>{loadingText}</Text>
            </View>
          ) : (
            <Text style={styles.buttonText}>Proceed to Payment</Text>
          )}
        </TouchableOpacity>
      </View>
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
  helperText: {
    fontSize: 12,
    color: theme.colors.textGray,
    marginTop: theme.spacing.xs,
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
  buttonLoading: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
  },
  buttonText: {
    color: theme.colors.white,
    fontSize: 16,
    fontWeight: '600',
  },
});

