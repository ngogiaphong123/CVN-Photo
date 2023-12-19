import { Link } from 'react-router-dom'

export function NotFound() {
  return (
    <div className="flex flex-col items-center justify-center flex-1 min-h-screen">
      <div className="text-black text-8xl text-foreground">404</div>
      <div className="text-2xl text-center text-muted-foreground">
        Page not found. Go back to the{' '}
        <Link className="text-primary" to="/">
          home page
        </Link>
        .
      </div>
    </div>
  )
}
