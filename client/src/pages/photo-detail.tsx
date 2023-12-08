import { usePhoto, usePhotos } from '@/hooks/photo.hook'
import { AdvancedImage } from '@cloudinary/react'
import { Cloudinary } from '@cloudinary/url-gen'
import { useParams } from 'react-router-dom'
import { Link } from 'react-router-dom'
import { Icon } from '@iconify/react'
import { NotFound } from '@/pages/not-found'
import PhotoInfo from '@components/photo-info'

export default function PhotoDetail() {
  const {
    data: photos,
    isLoading: photosLoading,
    isError: photosError,
  } = usePhotos()
  const { photoId } = useParams()
  const { data: photo, isLoading, isError } = usePhoto(photoId)
  if (isLoading || photosLoading)
    return (
      <div className="relative">
        <div className="flex min-h-screen">
          <div className="flex items-center justify-center w-9/12 bg-black"></div>
          <div className="flex items-center justify-center w-3/12 bg-white"></div>
        </div>
      </div>
    )
  if (isError || !photo || photosError || !photos)
    return (
      <div>
        <NotFound />
      </div>
    )
  const cld = new Cloudinary({
    cloud: {
      cloudName: 'giaphong',
    },
  })
  const image = cld.image(photo?.publicId).addAction(`fl_attachment`)
  const renderLeftArrow = () => {
    if (photos.length === 0) return <div className="w-1/12"></div>
    const index = photos.findIndex(photo => photo.id === photoId)
    if (index === 0) return <div></div>
    const prevPhoto = photos[index - 1]
    return (
      <Link
        to={`/photos/${prevPhoto.id}`}
        className="rounded-full hover:bg-gray-900"
      >
        {renderIcon('arrow-back-ios-new-rounded')}
      </Link>
    )
  }
  const renderRightArrow = () => {
    if (photos.length === 0) return <div className="w-1/12"></div>
    const index = photos.findIndex(photo => photo.id === photoId)
    if (index === photos.length - 1) return <div></div>
    const nextPhoto = photos[index + 1]
    return (
      <Link
        to={`/photos/${nextPhoto.id}`}
        className="rounded-full hover:bg-gray-900"
      >
        {renderIcon('arrow-forward-ios-rounded')}
      </Link>
    )
  }
  const renderIcon = (name: string) => {
    return (
      <div className="flex items-center justify-center w-12 h-12 rounded-full hover:opacity-80">
        <Icon
          icon={`material-symbols-light:${name}`}
          className="w-8 h-8 text-white"
        />
      </div>
    )
  }
  const renderHeader = () => {
    return (
      <div className="absolute top-0 left-0 z-10 flex items-center justify-between w-full h-16 px-4">
        <Link to="/photos">{renderIcon('arrow-left-alt')}</Link>
        <div className="flex items-center justify-center gap-4">
          <button className="hover:cursor-pointer">
            {renderIcon('delete-outline-rounded')}
          </button>
          <a
            href={image.toURL() as string}
            target=""
            className="hover:cursor-pointer"
          >
            {renderIcon('download-rounded')}
          </a>
          {renderIcon('favorite-outline-rounded')}
        </div>
      </div>
    )
  }
  return (
    <div key={photo.id} className="relative">
      <div className="flex min-h-screen">
        <div className="relative flex items-center justify-center w-full gap-4 px-4 bg-black lg:w-9/12">
          {renderHeader()}
          <div className="flex justify-start w-1/12">{renderLeftArrow()}</div>
          <div className="flex flex-col items-center justify-center w-10/12 max-h-screen">
            <AdvancedImage cldImg={image} className="max-h-screen" />
          </div>
          <div className="flex justify-end w-1/12">{renderRightArrow()}</div>
        </div>
        <PhotoInfo photo={photo} />
      </div>
    </div>
  )
}
