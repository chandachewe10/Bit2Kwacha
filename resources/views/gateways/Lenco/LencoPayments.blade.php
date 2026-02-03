<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lenco Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://pay.lenco.co/js/v1/inline.js"></script>
</head>

<body>

    <h3>Processing your payment...</h3>

    <script>
        function makePaymentsWithLenco() {
            LencoPay.getPaid({
                key: "{{ env('LENCO_PUBLIC_KEY') }}",
                reference: 'ref-' + Date.now(),
                email: "{{ $data['email'] ?? 'customer@bitkwik.com' }}",
                amount: {{ $data['total_amount'] }},
                currency: "ZMW",
                channels: ["card", "mobile-money"],
                customer: {
                    firstName: "{{ $data['name'] ?? 'Customer' }}",
                    lastName: "",
                    phone: "{{ $data['phone'] ?? '' }}",
                },

                onSuccess: function (response) {

                    let formData = new FormData();
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("payment_data", JSON.stringify(response));
                    formData.append("amount", "{{ $data['total_amount'] }}");

                    fetch("{{ route('complete.subscription') }}", {
                        method: "POST",
                        body: formData,
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                alert("✅ Bitcoin withdrawal created successfully! Scan the QR code using your wallet to receive the payment. The QRCode expires after 10 minutes.");

                                // Show QR code on the page
                                if (data.qr_code_path) {
                                    let qrContainer = document.createElement("div");
                                    qrContainer.style.cssText = "text-align: center; padding: 20px;";
                                    qrContainer.innerHTML = `
                                    <h3>Scan this QR code with your Lightning wallet:</h3>
                                    <img src="/images/qrcodes/${data.qr_code_path}" style="max-width: 400px; margin: 20px auto; display: block;" />
                                    <p>Or copy this Lightning invoice:</p>
                                    <p style="word-break: break-all; padding: 10px; background: #f0f0f0; border-radius: 5px;">${data.lnurl || ''}</p>
                                    <div style="margin-top: 20px;">
                                        <a href="lightning:${data.lnurl || ''}" style="display: inline-block; padding: 10px 20px; background-color: #F7931A; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">Open in Wallet</a>
                                    </div>
                                `;
                                    document.body.appendChild(qrContainer);
                                }
                            } else {
                                alert("⚠️ " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error("Fetch error:", error);
                            alert("❌ Something went wrong. Try again.");
                        });
                },

                onClose: function () {
                    alert('Payment was not complete, please try again.');
                    window.location.href = "/";
                },
                onConfirmationPending: function () {
                    alert('Your subscription will complete when payment is confirmed.');
                },
            });
        }

        window.addEventListener('DOMContentLoaded', makePaymentsWithLenco);
    </script>
</body>

</html>