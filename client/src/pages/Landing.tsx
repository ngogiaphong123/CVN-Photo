import Footer from '@components/layouts/footer'
import { Button } from '@components/ui/button'
import Hero from '@components/layouts/hero'
import { Link } from 'react-router-dom'

export function Landing() {
  return (
    <div className="flex flex-col h-full min-h-screen bg-background">
      <header className="sticky flex items-center justify-between h-16 p-4 border-b">
        <div className="text-primary">Web Photo</div>
        <div>
          <Link to="/login">
            <Button size={'lg'}>Go to WebPhoto</Button>
          </Link>
        </div>
      </header>
      <Hero />
      <Footer />
    </div>
  )
}
