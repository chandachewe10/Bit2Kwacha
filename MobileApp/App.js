// Polyfill for TextEncoder/TextDecoder (required for react-native-qrcode-svg)
import 'text-encoding';

import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { StatusBar } from 'expo-status-bar';
import { Ionicons } from '@expo/vector-icons';

import BuyScreen from './src/screens/BuyScreen';
import SellScreen from './src/screens/SellScreen';
import AirtimeScreen from './src/screens/AirtimeScreen';

const Tab = createBottomTabNavigator();

export default function App() {
  return (
    <NavigationContainer>
      <StatusBar style="light" />
      <Tab.Navigator
        screenOptions={({ route }) => ({
          tabBarIcon: ({ focused, color, size }) => {
            let iconName;

            if (route.name === 'Buy') {
              iconName = focused ? 'arrow-down-circle' : 'arrow-down-circle-outline';
            } else if (route.name === 'Sell') {
              iconName = focused ? 'arrow-up-circle' : 'arrow-up-circle-outline';
            } else if (route.name === 'Airtime') {
              iconName = focused ? 'phone-portrait' : 'phone-portrait-outline';
            }

            return <Ionicons name={iconName} size={size} color={color} />;
          },
          tabBarActiveTintColor: '#F7931A',
          tabBarInactiveTintColor: 'gray',
          headerStyle: {
            backgroundColor: '#F7931A',
          },
          headerTintColor: '#fff',
          headerTitleStyle: {
            fontWeight: 'bold',
          },
        })}
      >
        <Tab.Screen name="Buy" component={BuyScreen} />
        <Tab.Screen name="Sell" component={SellScreen} />
        <Tab.Screen name="Airtime" component={AirtimeScreen} options={{ title: 'Airtime' }} />
      </Tab.Navigator>
    </NavigationContainer>
  );
}

