import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { useState } from 'react'
import { AppDispatch, useAppSelector } from '@redux/store'
import { useDispatch } from 'react-redux'
import { useToast } from '@components/ui/use-toast'
import { uploadAvatar } from '@redux/slices/user.slice'
import { Button } from '@components/ui/button'

export default function AvatarDialog() {
  const [open, setOpen] = useState(false)
  const dispatch = useDispatch<AppDispatch>()
  const { toast } = useToast()
  const user = useAppSelector(state => state.user).user

  async function upload(input: File | null = null) {
    setOpen(false)
    toast({
      description: `Uploading your profile photo...`,
    })
    const result = await dispatch(uploadAvatar(input))
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toast({
        description: `Profile photo updated!`,
      })
    } catch (err: any) {
      toast({
        title: 'Oops!',
        description: `${err.message}`,
        variant: 'destructive',
      })
    }
  }

  const renderDialogContent = () => {
    return (
      <>
        {' '}
        <Button className="p-0" variant={'ghost'}>
          <label className="flex items-center justify-center w-full h-full cursor-pointer">
            <input
              type="file"
              className="hidden"
              onChange={e => {
                if (!e.target.files) return
                upload(e.target.files[0])
              }}
            />
            Upload new photo
          </label>
        </Button>
        <Button
          className="p-0 hover:bg-muted"
          variant={'ghost'}
          onClick={() => {
            upload()
          }}
        >
          <label className="text-red-500 cursor-pointer">
            Remove current photo
          </label>
        </Button>
      </>
    )
  }
  return (
    <div className="flex items-stretch justify-start w-10/12 gap-7">
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogTrigger>
          <Avatar className="w-12 h-12">
            <AvatarImage src={user.avatar} />
            <AvatarFallback>CN</AvatarFallback>
          </Avatar>
        </DialogTrigger>
        <div className="flex flex-col justify-center">
          <p>{user.displayName}</p>
          <DialogTrigger>
            <p className="font-bold text-primary">Change profile photo</p>
          </DialogTrigger>
        </div>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Change Profile Photo</DialogTitle>
          </DialogHeader>
          {renderDialogContent()}
        </DialogContent>
      </Dialog>
    </div>
  )
}
