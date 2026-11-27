import React, { useEffect, useRef, useState } from 'react';
import { ActivityIndicator, Text, TextInput, View } from 'react-native';
import { exchangeService } from '../services/exchangeService';
import { theme } from '../config/theme';
import { normalizeZambianPhone } from '../utils/zambianPhone';
import { resolveMobileMoneyOperator } from '../utils/mobileMoneyOperator';

const UNREGISTERED_MESSAGE = 'Number not registered for mobile money.';

/** Phone field that validates and resolves the mobile-money account as the user types. */
export default function RecipientPhoneInput({ value, onChangeText, onVerified, label = 'Mobile Money Number' }) {
  const [lookup, setLookup] = useState({ status: 'idle', name: '', operator: '', message: '' });
  const callbacks = useRef({ onVerified });
  callbacks.current.onVerified = onVerified;

  useEffect(() => {
    const normalized = normalizeZambianPhone(value);
    if (!normalized) {
      setLookup({ status: value ? 'invalid' : 'idle', name: '', operator: '', message: value ? 'Enter a valid 10-digit Zambian mobile number.' : '' });
      return undefined;
    }

    const operator = resolveMobileMoneyOperator(normalized);
    if (!operator) {
      setLookup({ status: 'invalid', name: '', operator: '', message: 'We could not identify this Zambian mobile network.' });
      return undefined;
    }

    let cancelled = false;
    setLookup({ status: 'loading', name: '', operator, message: 'Verifying number…' });
    const timer = setTimeout(async () => {
      try {
        const recipient = await exchangeService.resolveMobileMoney(normalized);
        if (cancelled) return;
        if (!recipient?.account_name) {
          setLookup({ status: 'error', name: '', operator, message: UNREGISTERED_MESSAGE });
          callbacks.current.onVerified?.(null);
          return;
        }
        const resolved = {
          phone: recipient.phone || normalized,
          account_name: recipient.account_name,
          operator: recipient.operator || operator,
        };
        setLookup({ status: 'verified', name: resolved.account_name, operator: resolved.operator, message: '' });
        callbacks.current.onVerified?.(resolved);
      } catch (error) {
        if (cancelled) return;
        const apiMessage = error.response?.data?.message || '';
        const isUnregistered = error.response?.status === 404 || /not\\s+registered|not\\s+found|no\\s+account/i.test(apiMessage);
        setLookup({
          status: 'error',
          name: '',
          operator,
          message: isUnregistered ? UNREGISTERED_MESSAGE : (apiMessage || 'Could not verify this number. Check it and try again.'),
        });
        callbacks.current.onVerified?.(null);
      }
    }, 500);

    return () => {
      cancelled = true;
      clearTimeout(timer);
    };
  }, [value]);

  const handleChange = (nextValue) => {
    setLookup({ status: 'idle', name: '', operator: '', message: '' });
    callbacks.current.onVerified?.(null);
    onChangeText(nextValue);
  };

  return (
    <View>
      <Text style={{ fontSize: 14, fontWeight: '600', color: theme.colors.text, marginBottom: theme.spacing.xs }}>{label}</Text>
      <View style={{ flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, paddingHorizontal: theme.spacing.md }}>
        <TextInput
          style={{ flex: 1, height: 50, fontSize: 16, color: theme.colors.text }}
          placeholder="09XXXXXXXX or +2609XXXXXXXX"
          placeholderTextColor={theme.colors.textGray}
          value={value}
          onChangeText={handleChange}
          keyboardType="phone-pad"
          maxLength={14}
          accessibilityLabel={label}
        />
        {lookup.status === 'loading' && <ActivityIndicator size="small" color={theme.colors.primary} />}
      </View>
      {lookup.status === 'verified' ? (
        <Text accessibilityLiveRegion="polite" style={{ color: '#16803c', fontSize: 13, marginTop: 6 }}>
          Verified {lookup.operator.toUpperCase()} recipient: {lookup.name}
        </Text>
      ) : lookup.message ? (
        <Text accessibilityLiveRegion="polite" style={{ color: lookup.status === 'loading' ? theme.colors.textGray : '#b42318', fontSize: 12, marginTop: 6 }}>
          {lookup.message}
        </Text>
      ) : null}
    </View>
  );
}
