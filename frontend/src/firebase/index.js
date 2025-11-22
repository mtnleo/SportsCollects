import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

const firebaseConfig = {
  apiKey: "AIzaSyDGGn9ow6u4yQT2PF2gh_ddCE1XJJobghU",
  authDomain: "primerparcial-4bc7d.firebaseapp.com",
  projectId: "primerparcial-4bc7d",
  storageBucket: "primerparcial-4bc7d.firebasestorage.app",
  messagingSenderId: "982085448900",
  appId: "1:982085448900:web:7fefd00565ddff58904591"
}

const app = initializeApp(firebaseConfig)
const auth = getAuth(app)

export { auth }
