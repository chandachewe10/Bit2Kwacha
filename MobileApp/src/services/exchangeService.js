import axios from 'axios';
import api, { apiEndpoints } from '../config/api';

export const exchangeService = {
  async getExchangeRates() {
    try {
      const response = await api.get(apiEndpoints.exchangeRates);
      return response.data;
    } catch (error) {
      console.error('Error fetching exchange rates:', error);
      throw error;
    }
  },

  async checkBalance(amountSats) {
    const response = await api.post(apiEndpoints.checkBalance, { amount_sats: amountSats });
    return response.data;
  },

  async checkLencoBalance(amountKwacha) {
    const response = await api.post(apiEndpoints.checkLencoBalance, { amount_kwacha: amountKwacha });
    return response.data;
  },

  async resolveMobileMoney(phone) {
    const response = await api.post(apiEndpoints.resolveMobileMoney, { phone });
    return response.data.data;
  },

  async generateInvoice(data) {
    const response = await api.post(apiEndpoints.generateInvoice, data);
    return response.data;
  },

  async generateAirtimeInvoice(data) {
    const response = await api.post(apiEndpoints.generateAirtimeInvoice, data);
    return response.data;
  },

  async completeSubscription(data) {
    const response = await api.post(apiEndpoints.completeSubscription, data);
    return response.data;
  },
};
