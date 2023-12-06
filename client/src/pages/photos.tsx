import { AppDispatch, useAppSelector } from '@redux/store'
import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { getPhotos } from '@redux/slices/photo.slice'
import PhotoByMonth from '@components/photo-by-month'
import { sortPhotosByMonthAndYear } from '@lib/helper'

export function Photos() {
  const photos = useAppSelector(state => state.photo).photos
  const dispatch = useDispatch<AppDispatch>()
  const processedPhotos = sortPhotosByMonthAndYear(photos)

  const renderPhotos = () => {
    if (photos.length === 0)
      return (
        <div className="flex items-center justify-center h-screen">
          <p className="text-2xl text-black">No photos yet.</p>
        </div>
      )
    return (
      <>
        {' '}
        <div className="flex flex-col h-screen pt-20">
          {processedPhotos.map(([key, photos]) => (
            <PhotoByMonth date={key} photos={photos} key={key} />
          ))}
        </div>
      </>
    )
  }

  useEffect(() => {
    dispatch(getPhotos())
  }, [])
  return <>{renderPhotos()}</>
}
