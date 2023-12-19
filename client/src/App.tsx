import { Route, Routes } from 'react-router-dom'
import { AuthGuard, GuestGuard, UserGuard } from '@components/middlewares'
import { Landing } from '@/pages/landing'
import { Login } from '@/pages/login'
import { Register } from '@/pages/register'
import { Photos } from '@/pages/photos'
import { NotFound } from '@pages/not-found'
import Profile from '@/pages/profile'
import UserLayout from '@components/layouts/user-layout'
import Category from '@/pages/category'
import Categories from '@/pages/categories'
import { AnimatePresence } from 'framer-motion'
import Photo from './pages/photo'

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
                <Route path="category" element={<Categories />} />
                <Route path="category/:categoryId" element={<Category />} />
              </Route>
              <Route path="photos/:photoId" element={<Photo />} />
            </Route>
          </Route>
          <Route path="*" element={<NotFound />} />
        </Routes>
      </AnimatePresence>
    </>
  )
}

export default App
