import { usePhoto } from '@/hooks/photo.hook'
import { useParams } from 'react-router-dom'

export default function PhotoDetail() {
  const { photoId } = useParams()
  const { data: photo, isLoading } = usePhoto(photoId)
  if (isLoading) return <div>loading</div>
  if (!photo) return <div>no data</div>
  return <div>{photo.name}</div>
}
