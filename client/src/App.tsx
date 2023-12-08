import { Route, Routes } from 'react-router-dom'
import { AuthGuard, GuestGuard, UserGuard } from '@components/middlewares'
import { Landing } from '@/pages/landing-page'
import { Login } from '@/pages/login-page'
import { Register } from '@/pages/register-page'
import { Photos } from '@/pages/photos'
import { NotFound } from '@pages/not-found'
import Profile from '@/pages/profile'
import UserLayout from '@components/layouts/user-layout'
import CategoryDetail from '@/pages/category-detail'
import Category from '@pages/category'
import { AnimatePresence } from 'framer-motion'
import PhotoDetail from './pages/photo-detail'

function App() {
  return (
    <>
      <AnimatePresence>
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
                <Route path="category" element={<Category />} />
                <Route
                  path="category/:categoryId"
                  element={<CategoryDetail />}
                />
              </Route>
              <Route path="photos/:photoId" element={<PhotoDetail />} />
            </Route>
          </Route>
          <Route path="*" element={<NotFound />} />
        </Routes>
      </AnimatePresence>
    </>
  )
}

export default App
