export function normalizeZambianPhone(phone) {
  const digits = String(phone || '').replace(/\D/g, '');
  const local = digits.startsWith('260') ? `0${digits.slice(3)}` : digits;
  // Keep the existing ten-digit 07/09 ranges and additionally accept new 055/056/057 allocations.
  return /^(?:0[79]\d{8}|0(?:55|56|57)\d{7})$/.test(local) ? local : null;
}
