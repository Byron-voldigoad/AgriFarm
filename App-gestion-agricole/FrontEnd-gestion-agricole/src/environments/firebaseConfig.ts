// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
export const firebaseConfig = {
  apiKey: "AIzaSyD26tQkw_TWpxwGo_AjGGSN6bPM-P0d4fg",
  authDomain: "agrifarm-edfd7.firebaseapp.com",
  projectId: "agrifarm-edfd7",
  storageBucket: "agrifarm-edfd7.firebasestorage.app",
  messagingSenderId: "547438617605",
  appId: "1:547438617605:web:47b5880f27d334b30f286c",
  measurementId: "G-YH49VB4JWC"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);