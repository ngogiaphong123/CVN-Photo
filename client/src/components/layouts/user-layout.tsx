import { Outlet } from 'react-router-dom'
import Navbar from './navbar'
import Sidebar from './sidebar'
import { ScrollArea } from '@components/ui/scroll-area'

export default function UserLayout() {
  return (
    <div className="flex-col min-h-screen">
      <Navbar />
      <div className="flex flex-1">
        <Sidebar className="flex justify-center w-2/12 border-r" />
        <ScrollArea className="w-full h-screen">
          <Outlet />
        </ScrollArea>
      </div>
    </div>
  )
}
