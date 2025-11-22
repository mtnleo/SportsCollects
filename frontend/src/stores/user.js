import { ref } from 'vue'
import { auth } from '../firebase'
import { onAuthStateChanged } from 'firebase/auth'
import axios from 'axios'

const currentUser = ref(null)
const userData = ref(null)

onAuthStateChanged(auth, async (user) => {
  currentUser.value = user
  
  if (user) {
    try {
      const response = await axios.post('http://localhost/TPFinalInterfaces/backend/api/usuarios.php', {
        firebase_uid: user.uid,
        email: user.email,
        nombre: user.displayName || user.email
      })
      userData.value = response.data
    } catch (error) {
      console.error('Error syncing user:', error)
    }
  } else {
    userData.value = null
  }
})

export function useUser() {
  return {
    currentUser,
    userData
  }
}
