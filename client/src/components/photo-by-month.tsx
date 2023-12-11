import { Photo } from '@redux/types/response.type'
import { convertToMonth } from '@lib/helper'
import PhotoImage from '@/components/photo-image'
import { useAppSelector } from '../redux/store'

export default function PhotoByMonth({
  date,
  photos,
  lastRef,
}: {
  date: string
  photos: Photo[]
  lastRef?: any
}) {
  const dateFormatted = new Date(date)
  const favorite = useAppSelector(state => state.category.favoriteCategory)
  return (
    <div className="flex flex-col justify-center gap-4 p-4">
      <div className="font-bold text-l md:text-2xl">
        {convertToMonth(dateFormatted.getMonth())} {dateFormatted.getFullYear()}{' '}
      </div>
      <div className="grid grid-cols-3 gap-2 md:grid-cols-4 lg:grid-cols-6">
        {photos.map((photo, i) => {
          if (i === photos.length - 1 && lastRef)
            return (
              <div key={photo.id} ref={lastRef}>
                <PhotoImage
                  photo={photo}
                  key={photo.id}
                  favoriteId={favorite.id}
                />
              </div>
            )
          return (
            <PhotoImage photo={photo} key={photo.id} favoriteId={favorite.id} />
          )
        })}
      </div>
    </div>
  )
}
