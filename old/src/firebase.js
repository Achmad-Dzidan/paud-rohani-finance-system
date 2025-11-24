// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyAxFeMsnK5FjkUgyNGDHZS2ZXyiowLdLEA",
  authDomain: "paud-rohani.firebaseapp.com",
  projectId: "paud-rohani",
  storageBucket: "paud-rohani.firebasestorage.app",
  messagingSenderId: "1051220684882",
  appId: "1:1051220684882:web:7d77a8367fa92b57c96f8b",
  measurementId: "G-SQHBZ6ST71"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);