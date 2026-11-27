import React, { useCallback, useEffect, useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert, ActivityIndicator, Modal, Linking } from 'react-native';
import QRCode from 'react-native-qrcode-svg';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';
import RecipientPhoneInput from '../components/RecipientPhoneInput';
import { normalizeZambianPhone } from '../utils/zambianPhone';

export default function SellScreen() {
  const [phone, setPhone] = useState('');
  const [recipient, setRecipient] = useState(null);
  const [amountSats, setAmountSats] = useState('');
  const [conversionRate, setConversionRate] = useState(0.023);
  const [serviceFeeRate, setServiceFeeRate] = useState(0.05);
  const [networkFee, setNetworkFee] = useState(10);
  const [loading, setLoading] = useState(false);
  const [calculations, setCalculations] = useState(null);
  const [invoiceModal, setInvoiceModal] = useState(false);
  const [invoiceData, setInvoiceData] = useState(null);
  const onRecipientVerified = useCallback((value) => setRecipient(value), []);

  useEffect(() => { fetchExchangeRates(); }, []);
  const fetchExchangeRates = async () => {
    try {
      const result = await exchangeService.getExchangeRates();
      if (result.status === 'success' && result.btc_zmw) setConversionRate(result.btc_zmw / 100000000);
      if (result.sell_service_fee_rate != null) setServiceFeeRate(Number(result.sell_service_fee_rate));
      if (result.sell_network_fee != null) setNetworkFee(Number(result.sell_network_fee));
    } catch (error) { console.error('Error fetching rates:', error); }
  };

  useEffect(() => {
    const amount = parseFloat(amountSats) || 0;
    if (amount < 500) { setCalculations(null); return; }
    const amountBtc = amount / 100000000;
    const conversionFee = amount * serviceFeeRate;
    const totalSats = amount + conversionFee + networkFee;
    const receiveKwacha = (amount - conversionFee) * conversionRate;
    setCalculations({ amountBtc, conversionFee, totalSats, receiveKwacha });
  }, [amountSats, conversionRate, serviceFeeRate, networkFee]);

  const handleSell = async () => {
    if (!normalizeZambianPhone(phone) || !recipient) {
      Alert.alert('Verify your number', 'Enter a valid Zambian mobile number and wait for the account name to be verified.');
      return;
    }
    if (!amountSats || parseFloat(amountSats) < 500 || !calculations) {
      Alert.alert('Validation Error', 'Enter an amount of at least 500 SATS.');
      return;
    }
    setLoading(true);
    try {
      const floatCheck = await exchangeService.checkLencoBalance(calculations.receiveKwacha);
      if (floatCheck.status === 'error') {
        Alert.alert('Float Check Failed', floatCheck.message || 'Unable to verify available float. Please try again later.');
        return;
      }
      if (floatCheck.sufficient === false) {
        Alert.alert('Temporarily Out of Float', `We've temporarily run out of float to process your sell order of ${calculations.receiveKwacha.toFixed(2)} ZMW. Please try again in a few minutes.`);
        return;
      }
      const result = await exchangeService.generateInvoice({
        phone: recipient.phone,
        recipient_name: recipient.account_name,
        amount_sats: parseFloat(amountSats),
        amount_btc: calculations.amountBtc,
        amount_kwacha: calculations.receiveKwacha,
        conversion_fee: Math.round(calculations.conversionFee),
        total_sats: Math.round(calculations.totalSats),
        network_fee: networkFee,
      });
      if (result.status === 'error') {
        Alert.alert(result.insufficient_float ? 'Temporarily Out of Float' : 'Error', result.message || 'Failed to generate invoice');
        return;
      }
      if (result.status === 'success') { setInvoiceData(result); setInvoiceModal(true); }
    } catch (error) {
      Alert.alert('Error', error.response?.data?.message || error.message || 'An error occurred. Please try again.');
    } finally { setLoading(false); }
  };

  const handleOpenWallet = async () => {
    if (!invoiceData?.bolt11) return;
    try { await Linking.openURL(`lightning:${invoiceData.bolt11}`); }
    catch { Alert.alert('Error', 'Could not open wallet. Please scan the QR code instead.'); }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.content}>
      <View style={styles.card}>
        <Text style={styles.title}>Sell Bitcoin for Kwacha</Text>
        <Text style={styles.subtitle}>Send Bitcoin, receive Kwacha to your mobile money</Text>
        <View style={styles.formGroup}>
          <RecipientPhoneInput value={phone} onChangeText={setPhone} onVerified={onRecipientVerified} />
        </View>
        <View style={styles.formGroup}>
          <Text style={styles.label}>Amount in Satoshis</Text>
          <View style={styles.inputWrapper}>
            <TextInput style={styles.input} placeholder="Enter amount" placeholderTextColor={theme.colors.textGray} value={amountSats} onChangeText={setAmountSats} keyboardType="number-pad" />
            <Text style={styles.suffix}>SATS</Text>
          </View>
        </View>
        {calculations && <View style={styles.calcBox}>
          <Row label="Amount (SATS):" value={parseFloat(amountSats).toLocaleString()} />
          <Row label="Service Fee:" value={`${Math.round(calculations.conversionFee).toLocaleString()} SATS`} />
          <Row label="Network Fee:" value={`${Math.round(networkFee).toLocaleString()} SATS`} />
          <Row label="Total SATS:" value={Math.round(calculations.totalSats).toLocaleString()} />
          <Row label="You'll Receive:" value={`${calculations.receiveKwacha.toFixed(2)} ZMW`} />
        </View>}
        <View style={styles.infoBadge}><Text style={styles.infoText}>Rate: 1 SAT = {conversionRate.toFixed(4)} ZMW | Min: 500 SATS</Text></View>
        <TouchableOpacity style={[styles.button, (loading || !recipient) && styles.buttonDisabled]} onPress={handleSell} disabled={loading || !recipient}>
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.buttonText}>Generate Lightning Invoice</Text>}
        </TouchableOpacity>
      </View>

      <Modal visible={invoiceModal} animationType="slide" transparent onRequestClose={() => setInvoiceModal(false)}>
        <View style={styles.modalOverlay}><View style={styles.modalContent}>
          <Text style={styles.modalTitle}>Invoice Generated!</Text>
          {invoiceData && <>
            <Text style={styles.modalText}>Scan the QR code with your Lightning wallet to pay.</Text>
            <View style={styles.qrContainer}><QRCode value={invoiceData.bolt11 || ''} size={230} color={theme.colors.text} backgroundColor={theme.colors.white} /></View>
            <TouchableOpacity style={styles.button} onPress={handleOpenWallet}><Text style={styles.buttonText}>Open in Bitcoin App</Text></TouchableOpacity>
            <Text style={styles.invoiceText} selectable>{invoiceData.bolt11}</Text>
            <TouchableOpacity style={styles.closeButton} onPress={() => setInvoiceModal(false)}><Text style={styles.buttonText}>Close</Text></TouchableOpacity>
          </>}
        </View></View>
      </Modal>
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
  inputWrapper: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, paddingHorizontal: theme.spacing.md }, input: { flex: 1, height: 50, fontSize: 16, color: theme.colors.text }, suffix: { fontSize: 14, color: theme.colors.textGray, marginLeft: theme.spacing.sm },
  calcBox: { backgroundColor: theme.colors.background, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginTop: theme.spacing.md, marginBottom: theme.spacing.md }, calcRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: theme.spacing.sm }, calcLabel: { fontSize: 14, color: theme.colors.textGray }, calcValue: { fontSize: 14, fontWeight: '600', color: theme.colors.text },
  infoBadge: { backgroundColor: 'rgba(247, 147, 26, 0.1)', borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginBottom: theme.spacing.md }, infoText: { fontSize: 12, color: theme.colors.primary },
  button: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.md }, buttonDisabled: { opacity: 0.5 }, buttonText: { color: theme.colors.white, fontSize: 16, fontWeight: '600' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'center', alignItems: 'center' }, modalContent: { backgroundColor: theme.colors.white, borderRadius: theme.borderRadius.xl, padding: theme.spacing.lg, width: '90%', maxWidth: 400, alignItems: 'center' },
  modalTitle: { fontSize: 20, fontWeight: 'bold', color: theme.colors.text, marginBottom: theme.spacing.md }, modalText: { fontSize: 14, color: theme.colors.textGray, textAlign: 'center', marginBottom: theme.spacing.lg }, qrContainer: { backgroundColor: theme.colors.white, padding: theme.spacing.md, borderRadius: theme.borderRadius.md, marginBottom: theme.spacing.md },
  invoiceText: { fontSize: 10, color: theme.colors.text, textAlign: 'center', marginTop: theme.spacing.md, marginBottom: theme.spacing.lg, padding: theme.spacing.sm, backgroundColor: theme.colors.background, borderRadius: theme.borderRadius.sm, fontFamily: 'monospace' }, closeButton: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, width: '100%', alignItems: 'center' },
});
