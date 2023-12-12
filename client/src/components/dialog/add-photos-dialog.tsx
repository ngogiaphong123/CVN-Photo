import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
} from '@components/ui/dialog'
import { useEffect, useState } from 'react'
import { cn } from '@/lib/utils'
import { DialogTrigger } from '@components/ui/dialog'
import { Button, buttonVariants } from '@components/ui/button'
import { Icon } from '@iconify/react'
import { toast } from '@components/ui/use-toast'
import PhotoAddDialog from './photos-add-dialog'
import { useQueryClient } from '@tanstack/react-query'
import { privateApi } from '@/lib/axios'
import { AppDispatch } from '@/redux/store'
import { useDispatch } from 'react-redux'
import { getCategories } from '@/redux/slices/category.slice'

export default function AddPhotosDialog({
  categoryId,
  categoryTitle,
}: {
  categoryId: string
  categoryTitle: string
}) {
  const [isOpen, setIsOpen] = useState(false)
  const [chosenPhotos, setChosenPhotos] = useState<string[]>([])
  const queryClient = useQueryClient()

  const dispatch = useDispatch<AppDispatch>()

  useEffect(() => {
    if (isOpen) setChosenPhotos([])
  }, [isOpen])
  const renderButton = () => {
    if (chosenPhotos.length === 0) {
      return (
        <Button
          variant={'ghost'}
          className="flex p-0 cursor-not-allowed bg-muted text-primary hover:bg-muted hover:text-accent"
          size={'lg'}
        >
          <div className="flex items-center gap-4 p-8">
            <Icon
              icon="material-symbols-light:add"
              width={24}
              className="text-primary"
            />
            Please choose at least one photo
          </div>
        </Button>
      )
    }
    return (
      <Button
        variant={'ghost'}
        className="flex p-0 text-white bg-accent hover:opacity-80"
        size={'lg'}
        onClick={async () => {
          chosenPhotos.forEach(async photoId => {
            const formData = new FormData()
            formData.append('photoId', photoId)
            formData.append('categoryId', categoryId)
            try {
              await privateApi.post(`/photo-category`, formData)
            } catch (err: any) {
              toast({
                title: 'Oops!',
                description: `${err.message}`,
                variant: 'destructive',
              })
            }
          })
          queryClient.removeQueries({
            queryKey: ['infinitePhotos'],
          })
          queryClient.removeQueries({
            queryKey: [`photosNotInCategory${categoryId}`],
          })
          await queryClient.invalidateQueries({
            queryKey: [`categoryPhotos${categoryId}`],
          })
          await queryClient.invalidateQueries({
            queryKey: [`${categoryId}`],
          })
          await dispatch(getCategories())
          toast({
            description: `Added ${chosenPhotos.length} photos to category ${categoryTitle}`,
          })
          setIsOpen(false)
          setChosenPhotos([])
        }}
      >
        <div className="flex items-center gap-4 p-8">
          <Icon
            icon="material-symbols-light:add"
            width={24}
            className="text-white"
          />
          Add {chosenPhotos.length} photos to your category
        </div>
      </Button>
    )
  }

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogTrigger className="w-2/12">
        <div
          className={cn(
            buttonVariants({ variant: 'ghost', size: 'lg' }),
            'flex p-0 text-white bg-accent hover:opacity-80 w-full',
          )}
        >
          <div className="flex items-center gap-4 p-8">
            <Icon
              icon="material-symbols-light:add"
              width={24}
              className="text-white"
            />
            Add photos
          </div>
        </div>{' '}
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          {' '}
          <p className="text-2xl text-primary">
            Add photos to category {categoryTitle}
          </p>
        </DialogHeader>
        <div className="overflow-y-scroll max-h-96">
          <PhotoAddDialog
            categoryId={categoryId}
            chosenPhotos={chosenPhotos}
            setChosenPhotos={setChosenPhotos}
          />
        </div>
        <DialogFooter>{renderButton()}</DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
