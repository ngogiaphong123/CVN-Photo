import { useEffect, useState } from 'react'
import DeletePhotoDialog from '@components/dialog/delete-photo-dialog'
import { renderPhotoDetailIcon } from '@lib/helper'
import {
  useAddPhotoToCategory,
  useRemovePhotoFromCategory,
} from '../hooks/category.hook'
import { AdvancedImage } from '@cloudinary/react'
import { CloudinaryImage } from '@cloudinary/url-gen/index'
import { Photo } from '../redux/types/response.type'
import { useNavigate } from 'react-router-dom'
import { Link } from 'react-router-dom'

export default function PhotoImageDetail({
  photo,
  image,
  favoriteId,
}: {
  photo: Photo
  image: CloudinaryImage
  favoriteId: string
}) {
  const navigate = useNavigate()
  const [isFavorite, setIsFavorite] = useState(0)
  useEffect(() => {
    setIsFavorite(photo.isFavorite)
  }, [photo])

  const { mutateAsync: addToFavorite } = useAddPhotoToCategory(
    favoriteId,
    photo.id,
  )
  const { mutateAsync: removeFromFavorite } = useRemovePhotoFromCategory(
    favoriteId,
    photo.id,
  )

  const renderLeftArrow = () => {
    if (photo.previous === null) return <div className="w-1/12"></div>
    return (
      <Link
        to={`/photos/${photo.previous}`}
        className="rounded-full hover:bg-gray-900"
      >
        {renderPhotoDetailIcon('arrow-back-ios-new-rounded')}
      </Link>
    )
  }
  const renderRightArrow = () => {
    if (photo.next === null) return <div className="w-1/12"></div>
    return (
      <Link
        to={`/photos/${photo.next}`}
        className="rounded-full hover:bg-gray-900"
      >
        {renderPhotoDetailIcon('arrow-forward-ios-rounded')}
      </Link>
    )
  }
  const renderFavoriteIcon = () => {
    if (isFavorite) {
      return renderPhotoDetailIcon('favorite-rounded', true)
    }
    return renderPhotoDetailIcon('favorite-outline-rounded', true)
  }
  const renderHeader = () => {
    return (
      <div className="absolute top-0 left-0 z-10 flex items-center justify-between w-full h-16 px-4">
        <div
          onClick={() => {
            navigate('/photos')
          }}
        >
          {renderPhotoDetailIcon('arrow-left-alt')}
        </div>
        <div className="flex items-center justify-center gap-4">
          <DeletePhotoDialog photoId={photo.id} />
          <a
            href={image.toURL() as string}
            target=""
            className="hover:cursor-pointer"
          >
            {renderPhotoDetailIcon('download-rounded')}
          </a>
          <button
            onClick={async () => {
              if (isFavorite) {
                await removeFromFavorite()
              } else {
                await addToFavorite()
              }
              setIsFavorite(isFavorite ^ 1)
            }}
          >
            {renderFavoriteIcon()}
          </button>
        </div>
      </div>
    )
  }
  return (
    <div className="relative flex items-center justify-center w-full gap-4 px-4 bg-black lg:w-9/12">
      {renderHeader()}
      <div className="flex justify-start w-1/12">{renderLeftArrow()}</div>
      <div className="flex flex-col items-center justify-center w-10/12 max-h-screen">
        <AdvancedImage cldImg={image} className="max-h-screen" />
      </div>
      <div className="flex justify-end w-1/12">{renderRightArrow()}</div>
    </div>
  )
}
