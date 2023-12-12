import React, { useEffect, useRef } from 'react'
import { useGetPhotosNotInCategoryByPage } from '@/hooks/category.hook'
import { cn } from '@/lib/utils'
import { useIntersection } from '@/hooks/useIntersection'

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
    rootMargin: '240px',
    threshold: 0,
  })
  const photos = data?.pages.flatMap(page => page)
  useEffect(() => {
    if (entry?.isIntersecting) {
      fetchNextPage()
    }
  }, [entry])
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
  if (photos?.length === 0 || !photos) {
    return (
      <div className="flex items-center justify-center w-full h-full text-xl text-muted-foreground">
        You have added all photos to this category
      </div>
    )
  }
  return (
    <div className="grid grid-cols-3 gap-2">
      {photos?.map((photo, i) => {
        if (i === photos.length - 1) {
          return (
            <div ref={ref} key={photo.id}>
              {renderPhotosInScrollArea(photo.url, photo.name, photo.id)}
            </div>
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
