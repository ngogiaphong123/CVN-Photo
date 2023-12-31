import { Photo } from '@redux/types/response.type'
import { Link } from 'react-router-dom'
import { useEffect, useState } from 'react'
import { renderPhotoDetailIcon } from '@lib/utils'
import { useAddPhotoToCategory } from '@/hooks/category/useAddPhotoToCategory'
import { useRemovePhotoFromCategory } from '@/hooks/category/useRemovePhotoFromCategory'

export default function PhotoImage({
  photo,
  favoriteId,
}: {
  photo: Photo
  favoriteId: string
}) {
  const [isFavorite, setIsFavorite] = useState(0)
  useEffect(() => {
    setIsFavorite(photo.isFavorite)
  }, [photo])

  const { mutateAsync: addToFavorite } = useAddPhotoToCategory(favoriteId)
  const { mutateAsync: removeFromFavorite } =
    useRemovePhotoFromCategory(favoriteId)
  const renderFavoriteIcon = () => {
    if (isFavorite) {
      return renderPhotoDetailIcon('favorite-rounded', true)
    }
    return renderPhotoDetailIcon('favorite-outline-rounded', true)
  }
  return (
    <div className="relative rounded-lg shadow-lg group group-hover:rounded-none">
      <div className="absolute top-0 right-0 transition-opacity duration-200 ease-in-out rounded-full opacity-0 group-hover:opacity-100">
        <button
          onClick={async () => {
            if (isFavorite) {
              await removeFromFavorite(photo.id)
            } else {
              await addToFavorite(photo.id)
            }
            setIsFavorite(isFavorite ^ 1)
          }}
        >
          {renderFavoriteIcon()}
        </button>
      </div>
      <Link to={`/photos/${photo.id}`}>
        <img
          key={photo.id}
          loading="lazy"
          className="object-cover transition-all duration-200 ease-in-out rounded-lg shadow-lg w-72 h-72 group-hover:rounded-none"
          src={photo.url}
          alt={photo.name}
        />
      </Link>
    </div>
  )
}
