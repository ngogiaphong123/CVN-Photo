import { Dialog, DialogContent, DialogHeader } from '@components/ui/dialog'
import React, { useEffect, useRef } from 'react'
import { useIntersection } from '@/hooks/useIntersection'
import { cn } from '@/lib/utils'
import { useGetPhotosNotInCategoryByPage } from '@/hooks/category.hook'

export default function AddPhotosDialog({
  categoryId,
  isOpen,
  setIsOpen,
  categoryTitle,
}: {
  categoryId: string
  isOpen: boolean
  setIsOpen: React.Dispatch<React.SetStateAction<boolean>>
  categoryTitle: string
}) {
  const { data, fetchNextPage } = useGetPhotosNotInCategoryByPage(categoryId)
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
    return (
      <img
        src={photoUrl}
        alt={photoName}
        className={cn(
          'object-cover rounded-lg shadow-lg w-72 h-72 hover:rounded-none',
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
              <div key={photo.id} ref={ref}>
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
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogContent>
        <DialogHeader>
          {' '}
          <p className="text-2xl text-primary">
            Add photos to category {categoryTitle}
          </p>
        </DialogHeader>
        <div className="overflow-y-scroll max-h-96">{renderPhotos()}</div>
      </DialogContent>
    </Dialog>
  )
}
