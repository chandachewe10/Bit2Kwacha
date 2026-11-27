import axios from "axios";

export const API_BASE_URL = "https://bit2kwacha.macroit.org";

const api = axios.create({
    baseURL: API_BASE_URL.endsWith("/") ? API_BASE_URL.slice(0, -1) : API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        ...(API_BASE_URL.includes("ngrok") && { "ngrok-skip-browser-warning": "true" }),
    },
    timeout: 30000,
    withCredentials: false,
});

api.interceptors.request.use(
    async (config) => {
        if (config.method !== "get") {
            try {
                // Laravel API requests are stateless; credentials remain server-side.
            } catch (error) {
                console.error("Error preparing API request:", error);
            }
        }
        return config;
    },
    (error) => Promise.reject(error),
);

export const apiEndpoints = {
    exchangeRates: "/api/exchange-rates",
    checkBalance: "/api/check-balance",
    checkLencoBalance: "/api/check-lenco-balance",
    resolveMobileMoney: "/api/resolve-mobile-money",
    generateInvoice: "/api/generate-invoice",
    generateAirtimeInvoice: "/api/generate-airtime-invoice",
    subscriptionPayment: "/subscription/payment",
    completeSubscription: "/api/complete-subscription",
};

export default api;
