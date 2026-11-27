export function resolveMobileMoneyOperator(phone) {
  const prefix = String(phone || '').replace(/\D/g, '').replace(/^260/, '0').slice(0, 3);
  if (['076', '096'].includes(prefix)) return 'mtn';
  if (['077', '097'].includes(prefix)) return 'airtel';
  if (['075', '095'].includes(prefix)) return 'zamtel';
  return null;
}
