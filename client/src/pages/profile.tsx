import { AppDispatch, useAppSelector } from '@redux/store'
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import * as z from 'zod'
import { Input } from '@components/ui/input'
import { Button } from '@components/ui/button'
import { cn } from '@lib/utils'
import { useDispatch } from 'react-redux'
import { updateProfile } from '@redux/slices/user.slice'
import { useToast } from '@components/ui/use-toast'
import AvatarDialog from '@/components/dialog/avatar-dialog'

const formSchema = z.object({
  email: z.string().email({
    message: 'Please enter a valid email.',
  }),
  displayName: z
    .string()
    .min(3, {
      message: 'Display name must be at least 3 characters',
    })
    .max(24, {
      message: 'Display name must be at most 24 characters',
    }),
})

export default function Profile() {
  const user = useAppSelector(state => state.user).user
  const dispatch = useDispatch<AppDispatch>()
  const { toast } = useToast()

  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: user.email,
      displayName: user.displayName,
    },
  })

  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    const result = await dispatch(updateProfile(values))
    toast({
      description: `Profile updated!`,
    })

    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toast({
        description: `Profile updated!`,
      })
    } catch (err: any) {
      toast({
        title: 'Oops!',
        description: `${err.message}`,
        variant: 'destructive',
      })
    }
  }

  return (
    <div className="flex flex-col items-start h-full px-32 py-8">
      <AvatarDialog />
      <Form {...form}>
        <form className="w-8/12 pt-4" onSubmit={form.handleSubmit(onSubmit)}>
          <div className="flex flex-col gap-4">
            <FormField
              control={form.control}
              name="email"
              render={({ field }) => (
                <FormItem className="flex items-center justify-center gap-8">
                  <FormLabel className="font-bold">Email </FormLabel>
                  <FormControl>
                    <Input placeholder="Email" {...field} disabled />
                  </FormControl>
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="displayName"
              render={({ field }) => (
                <div className="flex flex-col gap-4">
                  <FormItem className="flex items-center justify-center gap-8">
                    <FormLabel className="font-bold">Name </FormLabel>
                    <FormControl>
                      <Input
                        type="text"
                        placeholder={user.displayName}
                        {...field}
                      />
                    </FormControl>
                  </FormItem>
                  <FormMessage />
                </div>
              )}
            />
            <Button
              className={cn(
                form.formState.isSubmitting
                  ? 'bg-muted cursor-loading hover:bg-muted '
                  : 'bg-accent hover:bg-accent ',
                'py-2 px-4 rounded-lg shadow-lg text-white w-2/12',
              )}
              type="submit"
            >
              {form.formState.isSubmitting ? 'Loading...' : 'Submit'}
            </Button>
          </div>
        </form>
      </Form>
    </div>
  )
}
