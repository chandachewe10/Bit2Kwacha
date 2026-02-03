# WhatsApp Integration Setup

This guide explains how to configure WhatsApp notifications for sending QR codes and Lightning links after successful payments using the [chandachewe/whatsapp](https://github.com/chandachewe10/whatsapp-api) package.

## Features

After a successful Lenco payment, the system will automatically:
- Send a WhatsApp message with transaction details
- Include the Lightning link (LNURL)
- Send the QR code image

## Prerequisites

1. WhatsApp Business API account
2. Business Phone Number ID
3. Access Token from Meta for Developers

## Configuration

Add these to your `.env` file:

```env
WHATSAPP_VERSION=v19.0
WHATSAPP_BUSINESS_PHONE_NUMBER_ID=your_business_phone_number_id
WHATSAPP_TOKEN=your_whatsapp_access_token
WHATSAPP_BASE_URI=https://graph.facebook.com
```

**Note:** `WHATSAPP_BASE_URI` is optional and defaults to `https://graph.facebook.com` if not specified.

### Getting Your Credentials

1. Go to [Meta for Developers](https://developers.facebook.com/)
2. Create a WhatsApp Business App
3. Get your Business Phone Number ID from the app dashboard
4. Generate an Access Token with `whatsapp_business_messaging` permission
5. Copy the values to your `.env` file

## How It Works

1. User completes payment via Lenco
2. System creates LNURL withdrawal QR code
3. WhatsApp message is automatically sent to user's phone number with:
   - Payment confirmation
   - Transaction details (SATS, ZMW)
   - Lightning link (LNURL)
   - QR code image

## Phone Number Format

The system automatically formats Zambian phone numbers for WhatsApp Business API:
- `0971176778` → `260971176778`
- `+260971176778` → `260971176778`
- `260971176778` → `260971176778` (unchanged)

**Note:** WhatsApp Business API uses phone numbers without the `+` prefix.

## Testing

To test the WhatsApp integration:
1. Complete a test payment
2. Check Laravel logs for WhatsApp sending status
3. Verify the message is received on the test phone number

## Troubleshooting

- **Messages not sending**: Check your WhatsApp API credentials in `.env`
- **QR code not showing**: Ensure the QR code URL is publicly accessible
- **Phone number errors**: Verify phone number format in logs
- **BASE_URI errors**: Ensure `WHATSAPP_BASE_URI` is set (defaults to `https://graph.facebook.com`)

## Notes

- WhatsApp sending failures won't block the payment process
- All WhatsApp attempts are logged for debugging
- The QR code URL must be publicly accessible for the image to send

