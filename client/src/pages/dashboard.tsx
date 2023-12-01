import Sidebar from '@components/layouts/sidebar'
import Navbar from '@components/layouts/navbar'

export default function Photos() {
  return (
    <div className='flex flex-col min-h-screen'>
        <Navbar />
      <div className="flex flex-1">
        <Sidebar className="w-2/12 border-r" />
        <div className="flex-1">Photos</div>
      </div>
    </div>
  )
}
