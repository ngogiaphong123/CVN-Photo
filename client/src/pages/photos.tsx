import { AppDispatch, useAppSelector } from '@redux/store'
import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { getPhotos } from '@redux/slices/photo.slice'
import PhotoByMonth from '@components/photo-by-month'
import { sortPhotosByMonthAndYear } from '@lib/helper'
import PhotosSkeleton from '@components/layouts/photos-skeleton'
import { motion } from 'framer-motion'

export function Photos() {
  const photos = useAppSelector(state => state.photo).photos
  const isLoading = useAppSelector(state => state.photo).loading
  const dispatch = useDispatch<AppDispatch>()
  const processedPhotos = sortPhotosByMonthAndYear(photos)

  const renderPhotos = () => {
    if (isLoading) {
      return <PhotosSkeleton />
    }
    if (photos.length === 0) {
      return (
        <div className="flex flex-col h-screen pt-20">
          <div className="flex flex-col justify-center gap-4 p-4">
            <div className="text-xl font-bold uppercase md:text-2xl text-accent">
              No photos
            </div>
          </div>
        </div>
      )
    }
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
  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
    >
      {renderPhotos()}
    </motion.div>
  )
}
