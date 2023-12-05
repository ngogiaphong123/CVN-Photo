import Footer from '@/components/layouts/footer'
import Hero from '@/components/layouts/hero'
import Header from '@components/layouts/header'

export function Landing() {
  return (
    <div className="flex flex-col h-full min-h-screen bg-background">
      <Header />
      <Hero />
      <Footer />
    </div>
  )
}
