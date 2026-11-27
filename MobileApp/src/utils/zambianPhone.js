export function normalizeZambianPhone(phone) {
  const digits = String(phone || '').replace(/\D/g, '');
  const local = digits.startsWith('260') ? `0${digits.slice(3)}` : digits;
  return /^0(?:55|56|57|75|76|77|95|96|97)\d{7}$/.test(local) ? local : null;
}
