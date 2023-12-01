import { Link, useNavigate } from 'react-router-dom'
import { Button } from '@components/ui/button'
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
import { useToast } from '@components/ui/use-toast'

export default function Navbar() {
  const dispatch = useDispatch<AppDispatch>()
  const { toast } = useToast()
  const navigate = useNavigate()
  const user = useAppSelector(state => state.user).user
  const onLogout = async () => {
    const result = await dispatch(logout())
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toast({
        title: 'Logged out',
        description: 'You have successfully logged out.',
      })
      navigate('/')
    } catch (err: any) {
      toast({
        title: 'Oops!',
        description: `${err.message}`,
      })
    }
  }
  return (
    <header className="flex items-center justify-between px-8 py-4 border-b">
      <div>
        <p className="text-primary">
          <Link to="/">WebPhoto</Link>
        </p>
      </div>
      <div className="flex items-center gap-4">
        <Button
          variant={'ghost'}
          className="text-foreground hover:bg-muted hover:text-foreground"
        >
          <div className="flex items-center gap-4 ">
            <Icon icon="material-symbols:upload" width={24} />
            Upload
          </div>
        </Button>
        <div className="flex items-center">
          <DropdownMenu>
            <DropdownMenuTrigger>
              <Button
                className="flex items-center p-0 rounded-full"
                variant={'ghost'}
                size={'lg'}
              >
                <Avatar>
                  <AvatarImage src={user.avatar} />
                  <AvatarFallback>CN</AvatarFallback>
                </Avatar>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56">
              <DropdownMenuLabel>Hi, {user.displayName}</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuGroup>
                <DropdownMenuItem>Profile</DropdownMenuItem>
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
