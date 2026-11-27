export function normalizeZambianPhone(phone) {
  const digits = String(phone || '').replace(/\D/g, '');
  const local = digits.startsWith('260') ? `0${digits.slice(3)}` : digits;
  return /^0[79]\d{8}$/.test(local) ? local : null;
}
