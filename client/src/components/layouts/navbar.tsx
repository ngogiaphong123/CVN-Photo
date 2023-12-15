import { Link, useNavigate } from 'react-router-dom'
import { Button } from '@/components/ui/button'
import { Icon } from '@iconify/react'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { AppDispatch, useAppSelector } from '@redux/store'
import { useDispatch } from 'react-redux'
import { logout } from '@redux/slices/user.slice'
import { useUploadPhoto } from '@/hooks/photo.hook'
import { toastMessage } from '@lib/utils'

export default function Navbar() {
  const dispatch = useDispatch<AppDispatch>()
  const navigate = useNavigate()
  const user = useAppSelector(state => state.user).user
  const { mutateAsync: uploadNewPhotos } = useUploadPhoto()
  const upload = async (input: FileList) => {
    toastMessage('Uploading your photos...', 'default')
    try {
      await uploadNewPhotos(input)
      toastMessage('Uploaded photos!', 'default')
    } catch (err: any) {
      toastMessage(err.message, 'destructive')
    }
  }

  const onLogout = async () => {
    const result = await dispatch(logout())
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toastMessage('You have successfully logged out.', 'default')
      navigate('/')
    } catch (err: any) {
      toastMessage(err.message, 'destructive')
    }
  }
  return (
    <header className="fixed z-10 flex items-center justify-between w-full h-16 px-8 py-2 bg-white border-b">
      <div>
        <p className="text-primary">
          <Link to="/">WebPhoto</Link>
        </p>
      </div>
      <div className="flex items-center gap-4">
        <Button
          variant={'ghost'}
          className="p-0 text-white bg-accent hover:opacity-80"
          size={'lg'}
        >
          <label className="flex items-center gap-4 p-8">
            <Icon icon="material-symbols:upload" width={24} />
            <input
              multiple
              type="file"
              className="hidden"
              onChange={e => {
                if (!e.target.files?.length) return
                upload(e.target.files)
              }}
            />
            Upload
          </label>
        </Button>
        <div className="flex items-center">
          <DropdownMenu>
            <DropdownMenuTrigger className="p-0 rounded-full">
              <Avatar>
                <AvatarImage src={user.avatar} />
                <AvatarFallback>CN</AvatarFallback>
              </Avatar>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56">
              <DropdownMenuLabel>Hi, {user.displayName}</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuGroup>
                <DropdownMenuItem
                  onClick={() => {
                    navigate('/profile')
                  }}
                >
                  Profile
                </DropdownMenuItem>
              </DropdownMenuGroup>
              <DropdownMenuSeparator />
              <DropdownMenuItem onClick={onLogout}>Log out</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </header>
  )
}
