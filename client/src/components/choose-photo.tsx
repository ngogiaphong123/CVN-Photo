import { useGetPhotosByPage } from '@/hooks/photo.hook'
import { Dispatch, useEffect, useRef } from 'react'
import { useIntersection } from '@/hooks/useIntersection'
import { motion } from 'framer-motion'
import { FormControl } from '@components/ui/form'
import { cn } from '@lib/utils'

export default function ChoosePhoto({
  setUrl,
  setPublicId,
  url,
  publicId,
}: {
  setUrl: Dispatch<React.SetStateAction<string>>
  setPublicId: Dispatch<React.SetStateAction<string>>
  url: string
  publicId: string
}) {
  const { data, fetchNextPage } = useGetPhotosByPage()
  const lastImageRef = useRef<HTMLElement>(null)
  const { ref, entry } = useIntersection({
    root: lastImageRef.current,
    rootMargin: '100px',
    threshold: 0,
  })
  useEffect(() => {
    if (entry?.isIntersecting) {
      fetchNextPage()
    }
  }, [entry])
  const renderPhotosInScrollArea = (
    photoUrl: string,
    photoName: string,
    photoPublicId: string,
  ) => {
    const isChosen = url === photoUrl && publicId === photoPublicId
    return (
      <img
        onClick={() => {
          setUrl(photoUrl)
          setPublicId(photoPublicId)
        }}
        src={photoUrl}
        alt={photoName}
        className={cn(
          'object-cover rounded-lg shadow-lg w-72 h-72 hover:rounded-none transition-all duration-200 ease-in-out',
          isChosen ? 'border-2 border-primary scale-95' : '',
        )}
      />
    )
  }
  const renderPhotos = () => {
    const photos = data?.pages.flatMap(page => page)
    return (
      <div className="grid grid-cols-3 gap-2">
        {photos?.map((photo, i) => {
          if (i === photos.length - 1) {
            return (
              <div ref={ref} key={photo.id}>
                {renderPhotosInScrollArea(
                  photo.url,
                  photo.name,
                  photo.publicId,
                )}
              </div>
            )
          }
          return (
            <div key={photo.id}>
              {renderPhotosInScrollArea(photo.url, photo.name, photo.publicId)}
            </div>
          )
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
      <div className="overflow-y-scroll max-h-96">
        <FormControl>{renderPhotos()}</FormControl>
      </div>
    </motion.div>
  )
}
