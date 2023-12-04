import { Link } from 'react-router-dom'
import { Button } from '@/components/ui/button'

export default function Header() {
  return (
    <header className="sticky flex items-center justify-between h-16 p-4 border-b">
      <div className="text-primary">Web Photo</div>
      <div>
        <Link to="/login">
          <Button className="text-white" size={'lg'}>
            Go to WebPhoto
          </Button>
        </Link>
      </div>
    </header>
  )
}
