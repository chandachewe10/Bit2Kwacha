export function resolveMobileMoneyOperator(phone) {
  const digits = String(phone || '').replace(/\D/g, '');
  const local = digits.startsWith('260') ? `0${digits.slice(3)}` : digits;
  const prefix = local.slice(0, 3);
  if (['056', '076', '096'].includes(prefix)) return 'mtn';
  if (['057', '077', '097'].includes(prefix)) return 'airtel';
  if (['055', '075', '095'].includes(prefix)) return 'zamtel';
  return null;
}
