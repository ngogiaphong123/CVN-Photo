import { Photo } from '@redux/types/response.type'
import { convertToMonth } from '@lib/helper'

export default function PhotoByMonth({
  date,
  photos,
}: {
  date: string
  photos: Photo[]
}) {
  const dateFormatted = new Date(date)

  return (
    <div className="flex flex-col justify-center gap-4 p-4">
      <div className="text-2xl font-bold">
        {convertToMonth(dateFormatted.getMonth())} {dateFormatted.getFullYear()}{' '}
      </div>
      <div className="flex flex-wrap items-center justify-start gap-4">
        {photos.map(photo => (
          <div className="" key={photo.id}>
            <img
              loading="lazy"
              className="object-cover h-48 transition-all duration-200 ease-in-out shadow-lg hover:scale-105"
              src={photo.url}
              alt={photo.name}
            />
          </div>
        ))}
      </div>
    </div>
  )
}
