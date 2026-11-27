import React, { useCallback, useEffect, useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert, ActivityIndicator, Linking } from 'react-native';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';
import { API_BASE_URL } from '../config/api';
import RecipientPhoneInput from '../components/RecipientPhoneInput';
import { normalizeZambianPhone } from '../utils/zambianPhone';

export default function BuyScreen() {
  const [phone, setPhone] = useState('');
  const [recipient, setRecipient] = useState(null);
  const [amountKwacha, setAmountKwacha] = useState('');
  const [conversionRate, setConversionRate] = useState(0.023);
  const [serviceFeeRate, setServiceFeeRate] = useState(0.05);
  const [networkFee, setNetworkFee] = useState(1);
  const [loading, setLoading] = useState(false);
  const [loadingText, setLoadingText] = useState('Proceed to Payment');
  const [calculations, setCalculations] = useState(null);
  const onRecipientVerified = useCallback((value) => setRecipient(value), []);

  useEffect(() => { fetchExchangeRates(); }, []);

  const fetchExchangeRates = async () => {
    try {
      const result = await exchangeService.getExchangeRates();
      if (result.status === 'success' && result.btc_zmw) setConversionRate(result.btc_zmw / 100000000);
      if (result.buy_service_fee_rate != null) setServiceFeeRate(Number(result.buy_service_fee_rate));
      if (result.buy_network_fee != null) setNetworkFee(Number(result.buy_network_fee));
    } catch (error) { console.error('Error fetching rates:', error); }
  };

  useEffect(() => {
    const amount = parseFloat(amountKwacha) || 0;
    if (amount < 20) { setCalculations(null); return; }
    const amountSats = amount / conversionRate;
    setCalculations({ amountSats, amountBtc: amountSats / 100000000, conversionFee: amount * serviceFeeRate, totalAmount: amount + amount * serviceFeeRate + networkFee });
  }, [amountKwacha, conversionRate, serviceFeeRate, networkFee]);

  const handleBuy = async () => {
    if (!normalizeZambianPhone(phone) || !recipient) {
      Alert.alert('Verify your number', 'Enter a valid Zambian mobile number and wait for the account name to be verified.');
      return;
    }
    if (!amountKwacha || parseFloat(amountKwacha) < 20) {
      Alert.alert('Validation Error', 'Please enter an amount of at least 20 ZMW.');
      return;
    }
    if (!calculations) { Alert.alert('Error', 'Please wait for calculations to complete'); return; }

    setLoading(true);
    setLoadingText('Checking Balance...');
    try {
      const balanceCheck = await exchangeService.checkBalance(calculations.amountSats);
      if (balanceCheck.status === 'error') {
        Alert.alert('Balance Check Failed', balanceCheck.message || 'Unable to verify account balance');
        setLoadingText('Proceed to Payment');
        return;
      }
      if (!balanceCheck.sufficient) {
        Alert.alert('Insufficient Balance', `We currently don't have enough Bitcoin in stock. You requested ${Math.round(balanceCheck.required_sats || calculations.amountSats).toLocaleString()} SATS. Please try again in a few minutes.`);
        setLoadingText('Proceed to Payment');
        return;
      }

      setLoadingText('Proceeding to Payment...');
      const params = new URLSearchParams({
        phone: recipient.phone,
        name: recipient.account_name,
        amount_kwacha: parseFloat(amountKwacha).toString(),
        amount_sats: Math.round(calculations.amountSats).toString(),
        amount_btc: calculations.amountBtc.toString(),
        conversion_fee: calculations.conversionFee.toString(),
        network_fee: networkFee.toString(),
        total_amount: calculations.totalAmount.toString(),
        type: 'buy',
      });
      const fullUrl = `${API_BASE_URL}/subscription/payment?${params.toString()}`;
      if (await Linking.canOpenURL(fullUrl)) await Linking.openURL(fullUrl);
      else Alert.alert('Error', 'Unable to open payment page. Please check your internet connection.');
    } catch (error) {
      console.error('Payment error:', error);
      Alert.alert('Error', error.response?.data?.message || error.message || 'An error occurred. Please try again.');
      setLoadingText('Proceed to Payment');
    } finally { setLoading(false); }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.card}>
        <Text style={styles.title}>Buy Bitcoin with Kwacha</Text>
        <Text style={styles.subtitle}>Pay with mobile money or card, receive Bitcoin instantly</Text>
        <View style={styles.formGroup}>
          <RecipientPhoneInput value={phone} onChangeText={setPhone} onVerified={onRecipientVerified} label="Phone number to make the mobile money payment" />
        </View>
        <View style={styles.formGroup}>
          <Text style={styles.label}>Amount in Kwacha (ZMW)</Text>
          <View style={styles.inputWrapper}>
            <TextInput style={styles.input} placeholder="Enter amount" placeholderTextColor={theme.colors.textGray} value={amountKwacha} onChangeText={setAmountKwacha} keyboardType="decimal-pad" />
            <Text style={styles.suffix}>ZMW</Text>
          </View>
        </View>
        {calculations && <View style={styles.calcBox}>
          <Row label="Amount (ZMW):" value={parseFloat(amountKwacha).toFixed(2)} />
          <Row label="Service Fee:" value={`${calculations.conversionFee.toFixed(2)} ZMW`} />
          <Row label="Network Fee:" value={`${networkFee.toFixed(2)} ZMW`} />
          <Row label="Total to Pay:" value={`${calculations.totalAmount.toFixed(2)} ZMW`} />
          <Row label="You'll Receive:" value={`${calculations.amountBtc.toFixed(8)} BTC`} />
          <Row label="SATS:" value={Math.round(calculations.amountSats).toLocaleString()} />
        </View>}
        <View style={styles.infoBadge}><Text style={styles.infoText}>Rate: 1 ZMW = ~{(1 / conversionRate).toFixed(2)} SATS | Min: 20 ZMW</Text></View>
        <TouchableOpacity style={[styles.button, (loading || !recipient) && styles.buttonDisabled]} onPress={handleBuy} disabled={loading || !recipient}>
          {loading ? <View style={styles.buttonLoading}><ActivityIndicator color="#fff" size="small" /><Text style={styles.buttonText}>{loadingText}</Text></View> : <Text style={styles.buttonText}>Proceed to Payment</Text>}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

function Row({ label, value }) {
  return <View style={styles.calcRow}><Text style={styles.calcLabel}>{label}</Text><Text style={styles.calcValue}>{value}</Text></View>;
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background }, content: { padding: theme.spacing.md },
  card: { backgroundColor: theme.colors.white, borderRadius: theme.borderRadius.xl, padding: theme.spacing.lg, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.1, shadowRadius: 8, elevation: 3 },
  title: { fontSize: 24, fontWeight: 'bold', color: theme.colors.text, marginBottom: theme.spacing.xs }, subtitle: { fontSize: 14, color: theme.colors.textGray, marginBottom: theme.spacing.lg },
  formGroup: { marginBottom: theme.spacing.md }, label: { fontSize: 14, fontWeight: '600', color: theme.colors.text, marginBottom: theme.spacing.xs },
  inputWrapper: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, paddingHorizontal: theme.spacing.md },
  input: { flex: 1, height: 50, fontSize: 16, color: theme.colors.text }, suffix: { fontSize: 14, color: theme.colors.textGray, marginLeft: theme.spacing.sm },
  calcBox: { backgroundColor: theme.colors.background, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginTop: theme.spacing.md, marginBottom: theme.spacing.md },
  calcRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: theme.spacing.sm }, calcLabel: { fontSize: 14, color: theme.colors.textGray }, calcValue: { fontSize: 14, fontWeight: '600', color: theme.colors.text },
  infoBadge: { backgroundColor: 'rgba(247, 147, 26, 0.1)', borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginBottom: theme.spacing.md }, infoText: { fontSize: 12, color: theme.colors.primary },
  button: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.md }, buttonDisabled: { opacity: 0.5 },
  buttonLoading: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8 }, buttonText: { color: theme.colors.white, fontSize: 16, fontWeight: '600' },
});
