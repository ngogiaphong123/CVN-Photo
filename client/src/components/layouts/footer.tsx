import { Icon } from '@iconify/react'
import { Link } from 'react-router-dom'

export default function Footer() {
  return (
    <footer className="flex items-start justify-between h-16 p-4 border-t">
      <div className="flex gap-4">
        <div className="text-primary">Web Photo</div>
        <div className="flex gap-4">
          <p>Developed by Gia Phong Ngo</p>
        </div>
      </div>
      <div className="flex gap-4">
        <Link to="https://www.instagram.com/giaphong.ngo13/">
          <Icon icon="skill-icons:instagram" />
        </Link>
        <Link to="https://www.facebook.com/giaphong.ngo.13/">
          <Icon icon="logos:facebook" />
        </Link>
        <Icon icon="openmoji:youtube" />
        <i></i>
      </div>
    </footer>
  )
}
