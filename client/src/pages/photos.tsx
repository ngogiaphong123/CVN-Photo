import PhotoByMonth from '@components/photo-by-month'
import { sortPhotosByMonthAndYear } from '@lib/utils'
import { motion } from 'framer-motion'
import { useEffect, useRef } from 'react'
import { useIntersection } from '@/hooks/useIntersection'
import { useGetPhotosByPage } from '../hooks/photo/useGetPhotosByPage'

export function Photos() {
  const { data, fetchNextPage } = useGetPhotosByPage()
  const lastImageRef = useRef<HTMLElement>(null)
  const { ref, entry } = useIntersection({
    root: lastImageRef.current,
    rootMargin: '240px',
    threshold: 0,
  })
  useEffect(() => {
    if (entry?.isIntersecting) {
      fetchNextPage()
    }
  }, [entry])
  const renderPhotos = () => {
    const photos = data?.pages.flatMap(page => page)
    const processedPhotos = sortPhotosByMonthAndYear(photos)
    return (
      <div className="pt-20">
        {processedPhotos.map(([key, photos], i) => {
          if (i === processedPhotos.length - 1) {
            return (
              <div key={key}>
                <PhotoByMonth
                  date={key}
                  photos={photos}
                  key={key}
                  lastRef={ref}
                />
              </div>
            )
          }
          return <PhotoByMonth date={key} photos={photos} key={key} />
        })}
      </div>
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
