import { Outlet } from 'react-router-dom'
import Navbar from './navbar'
import Sidebar from './sidebar'

export default function UserLayout() {
  return (
    <div className="flex flex-col min-h-screen">
      <Navbar />
      <div className="flex flex-1">
        <Sidebar className="w-2/12" />
        <div className="flex-1">
          <Outlet />
        </div>
      </div>
    </div>
  )
}
