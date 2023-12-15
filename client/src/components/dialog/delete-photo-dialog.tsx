import { useDeletePhoto } from '@/hooks/photo.hook'
import { Dialog, DialogContent, DialogTrigger } from '@components/ui/dialog'
import { useNavigate } from 'react-router-dom'
import { renderPhotoDetailIcon } from '@lib/utils'

export default function DeletePhotoDialog({ photoId }: { photoId: string }) {
  const { mutateAsync: deletePhoto } = useDeletePhoto(photoId)
  const navigate = useNavigate()

  return (
    <Dialog>
      <DialogTrigger>
        <div className="hover:cursor-pointer">
          {renderPhotoDetailIcon('delete-outline-rounded')}
        </div>
      </DialogTrigger>
      <DialogContent>
        <div className="flex flex-col items-center justify-center w-full gap-4 p-4">
          <div className="font-bold text-l md:text-l">
            Are you sure you want to delete this photo?
          </div>
          <div className="flex items-center justify-center gap-4">
            <button
              className="px-4 py-2 text-white rounded-lg bg-destructive hover:opacity-80"
              onClick={() => {
                deletePhoto()
                navigate(-1)
              }}
            >
              Delete
            </button>
            <button className="px-4 py-2 text-white rounded-lg bg-primary hover:opacity-80">
              Cancel
            </button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
