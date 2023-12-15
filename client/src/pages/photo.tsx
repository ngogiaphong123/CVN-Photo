import { Cloudinary } from '@cloudinary/url-gen'
import { useParams } from 'react-router-dom'
import { NotFound } from '@/pages/not-found'
import PhotoInfo from '@components/photo-info'
import PhotoImageDetail from '@components/photo-image-detail'
import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { AppDispatch, useAppSelector } from '@redux/store'
import { getCategories } from '@redux/slices/category.slice'
import { usePhoto } from '../hooks/photo/usePhoto'

export default function Photo() {
  const { photoId } = useParams()
  const { data: photo, isLoading, isError } = usePhoto(photoId)
  const dispatch = useDispatch<AppDispatch>()
  useEffect(() => {
    dispatch(getCategories())
  }, [])
  const favoriteId = useAppSelector(state => state.category.favoriteCategory.id)
  if (isLoading) {
    return (
      <div className="relative">
        <div className="flex min-h-screen">
          <div className="flex items-center justify-center w-9/12 bg-black"></div>
          <div className="flex items-center justify-center w-3/12 bg-white"></div>
        </div>
      </div>
    )
  }

  if (isError || !photo) return <NotFound />
  const cld = new Cloudinary({
    cloud: {
      cloudName: 'giaphong',
    },
  })
  const image = cld.image(photo?.publicId).addAction(`fl_attachment`)
  return (
    <div key={photo.id} className="relative">
      <div className="flex min-h-screen">
        <PhotoImageDetail photo={photo} image={image} favoriteId={favoriteId} />
        <PhotoInfo photo={photo} />
      </div>
    </div>
  )
}
