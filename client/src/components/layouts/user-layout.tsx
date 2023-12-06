import { Outlet } from 'react-router-dom'
import Navbar from './navbar'
import Sidebar from './sidebar'
import { ScrollArea } from '../ui/scroll-area'

export default function UserLayout() {
  return (
    <div className="flex-col min-h-screen">
      <Navbar />
      <div className="flex flex-1">
        <Sidebar className="w-2/12" />
        <ScrollArea className="w-full h-screen">
            <Outlet/>
        </ScrollArea>
      </div>
    </div>
  )
}
