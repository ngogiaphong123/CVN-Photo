import { Photo } from '@redux/types/response.type'
import { convertToMonth } from '@lib/helper'
import PhotoImage from '@/components/photo-image'

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
      <div className="font-bold text-l md:text-2xl">
        {convertToMonth(dateFormatted.getMonth())} {dateFormatted.getFullYear()}{' '}
      </div>
      <div className="grid grid-cols-3 gap-2 md:grid-cols-4 lg:grid-cols-6">
        {photos.map(photo => (
          <PhotoImage photo={photo} key={photo.id} />
        ))}
      </div>
    </div>
  )
}
