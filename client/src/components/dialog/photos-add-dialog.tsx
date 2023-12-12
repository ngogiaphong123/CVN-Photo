import React, { useEffect, useRef } from 'react'
import { useGetPhotosNotInCategoryByPage } from '@/hooks/category.hook'
import { useIntersection } from '@/hooks/useIntersection'
import { cn } from '@/lib/utils'

export default function PhotoAddDialog({
  categoryId,
  chosenPhotos,
  setChosenPhotos,
}: {
  categoryId: string
  chosenPhotos: string[]
  setChosenPhotos: React.Dispatch<React.SetStateAction<string[]>>
}) {
  const { data, fetchNextPage } = useGetPhotosNotInCategoryByPage(categoryId)
  const lastImageRef = useRef<HTMLElement>(null)
  const { ref, entry } = useIntersection({
    root: lastImageRef.current,
    rootMargin: '100px',
    threshold: 1,
  })
  const photos = data?.pages.flatMap(page => page)

  const renderPhotosInScrollArea = (
    photoUrl: string,
    photoName: string,
    photoId: string,
  ) => {
    let chosen = false
    if (chosenPhotos.includes(photoId)) {
      chosen = true
    }
    return (
      <img
        onClick={() => {
          if (chosen) {
            setChosenPhotos(chosenPhotos.filter(photo => photo !== photoId))
          } else {
            setChosenPhotos([...chosenPhotos, photoId])
          }
        }}
        src={photoUrl}
        alt={photoName}
        className={cn(
          'object-cover rounded-lg shadow-lg w-72 h-72 hover:rounded-none transition-all duration-200 ease-in-out',
          chosen ? 'border-2 border-primary scale-95 ' : '',
        )}
      />
    )
  }
  useEffect(() => {
    if (entry?.isIntersecting) {
      fetchNextPage()
    }
  }, [entry])
  return (
    <div className="grid grid-cols-3 gap-2">
      {photos?.map((photo, i) => {
        if (i === photos.length - 1) {
          return (
            <section key={photo.id} ref={ref}>
              {renderPhotosInScrollArea(photo.url, photo.name, photo.id)}
            </section>
          )
        }
        return (
          <div key={photo.id}>
            {renderPhotosInScrollArea(photo.url, photo.name, photo.id)}
          </div>
        )
      })}
    </div>
  )
}
