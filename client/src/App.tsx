import { Route, Routes } from 'react-router-dom'
import { Login } from '@/pages/login'
import { Register } from '@/pages/register'
import { Landing } from '@pages/landing'
import Photos from '@pages/dashboard'
import NotFound from '@/pages/not-found'

function App() {
  return (
    <>
      <Routes>
        <Route path="/" element={<Landing />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/photos" element={<Photos />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </>
  )
}

export default App
