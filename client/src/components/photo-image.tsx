import { Photo } from '@redux/types/response.type'
import { Link } from 'react-router-dom'

export default function PhotoImage({ photo }: { photo: Photo }) {
  return (
    <Link to={`/photos/${photo.id}`}>
      <img
        key={photo.id}
        loading="lazy"
        className="object-cover transition-all duration-200 ease-in-out rounded-lg shadow-lg w-72 h-72 hover:scale-105 hover:rounded-none"
        src={photo.url}
        alt={photo.name}
      />
    </Link>
  )
}
