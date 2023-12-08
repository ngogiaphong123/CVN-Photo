import PhotoByMonth from '@components/photo-by-month'
import { sortPhotosByMonthAndYear } from '@lib/helper'
import PhotosSkeleton from '@components/layouts/photos-skeleton'
import { motion } from 'framer-motion'
import { usePhotos } from '@/hooks/photo.hook'

export function Photos() {
  const { data, isLoading, isError } = usePhotos()
  const renderPhotos = () => {
    if (isLoading && !data)
      return (
        <div className="pt-20">
          <PhotosSkeleton />
        </div>
      )
    if (isError) return <div>error</div>
    if (!data) return <div>no data</div>
    const processedPhotos = sortPhotosByMonthAndYear(data)
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
