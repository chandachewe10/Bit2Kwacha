import api, { apiEndpoints } from '../config/api';

export const exchangeService = {
  /**
   * Fetch live exchange rates
   */
  async getExchangeRates() {
    try {
      const response = await api.get(apiEndpoints.exchangeRates);
      return response.data;
    } catch (error) {
      console.error('Error fetching exchange rates:', error);
      if (error.response) {
        // Server responded with error
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
      } else if (error.request) {
        // Request made but no response
        console.error('No response received:', error.request);
      } else {
        // Error setting up request
        console.error('Error:', error.message);
      }
      throw error;
    }
  },

  /**
   * Check account balance
   */
  async checkBalance(amountSats) {
    try {
      const response = await api.post(apiEndpoints.checkBalance, {
        amount_sats: amountSats,
      });
      return response.data;
    } catch (error) {
      console.error('Error checking balance:', error);
      throw error;
    }
  },

  /**
   * Check Lenco wallet float balance (ZMW) before selling Bitcoin
   */
  async checkLencoBalance(amountKwacha) {
    try {
      const response = await api.post(apiEndpoints.checkLencoBalance, {
        amount_kwacha: amountKwacha,
      });
      return response.data;
    } catch (error) {
      console.error('Error checking Lenco balance:', error);
      throw error;
    }
  },

  /**
   * Generate Lightning invoice for selling Bitcoin
   */
  async generateInvoice(data) {
    try {
      const response = await api.post(apiEndpoints.generateInvoice, data);
      return response.data;
    } catch (error) {
      console.error('Error generating invoice:', error);
      throw error;
    }
  },

  /**
   * Complete subscription/payment for buying Bitcoin
   */
  async completeSubscription(data) {
    try {
      const response = await api.post(apiEndpoints.completeSubscription, data);
      return response.data;
    } catch (error) {
      console.error('Error completing subscription:', error);
      throw error;
    }
  },
};

