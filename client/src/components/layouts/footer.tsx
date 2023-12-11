import { Icon } from '@iconify/react'

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
        <Icon icon="skill-icons:instagram" />
        <Icon icon="logos:facebook" />
        <Icon icon="openmoji:youtube" />
        <i></i>
      </div>
    </footer>
  )
}
