import { Route, Routes } from 'react-router-dom'
import { AuthGuard, GuestGuard, UserGuard } from '@components/middlewares'
import { Landing } from '@/pages/landing-page'
import { Login } from '@/pages/login-page'
import { Register } from '@/pages/register-page'
import { Photos } from '@/pages/photos'
import { NotFound } from '@pages/not-found'
import Profile from '@/pages/profile'
import UserLayout from '@components/layouts/user-layout'

function App() {
  return (
    <>
      <Routes>
        <Route path="" element={<AuthGuard />}>
          <Route path="" element={<GuestGuard />}>
            <Route path="/" element={<Landing />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
          </Route>

          <Route path="" element={<UserGuard />}>
            <Route path="/" element={<UserLayout />}>
              <Route path="/photos" element={<Photos />} />
              <Route path="/profile" element={<Profile />} />
            </Route>
          </Route>
        </Route>
        <Route path="*" element={<NotFound />} />
      </Routes>
    </>
  )
}

export default App
