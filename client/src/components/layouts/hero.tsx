import { Button } from '@/components/ui/button'
import { Link } from 'react-router-dom'

export default function Hero() {
  return (
    <div className="flex flex-col items-center justify-center flex-1 gap-8">
      <div className="text-black text-8xl text-foreground">
        The place your memories <br />
        <p className="text-center">call home</p>
      </div>
      <div className="text-2xl text-center text-muted-foreground">
        A treasury of lingering moments. <br />
      </div>
      <div>
        <Link to="/login">
          <Button className="text-white" size={'lg'}>
            Go to WebPhoto
          </Button>
        </Link>
      </div>
    </div>
  )
}
