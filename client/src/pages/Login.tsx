import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import * as z from 'zod'
import { useNavigate } from 'react-router-dom'

import { Button } from '@/components/ui/button'
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Link } from 'react-router-dom'
import { useDispatch } from 'react-redux'
import { AppDispatch } from '@redux/store'
import { login } from '@redux/slices/user.slice'
import { useToast } from '@components/ui/use-toast'

const formSchema = z.object({
  email: z.string().email({
    message: 'Please enter a valid email.',
  }),
  password: z
    .string()
    .min(6, {
      message: 'Password must be at least 6 characters',
    })
    .max(32, {
      message: 'Password must be at most 32 characters',
    }),
})

export function Login() {
  const dispatch = useDispatch<AppDispatch>()
  const navigate = useNavigate()
  const { toast } = useToast()

  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
    },
  })
  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    const result = await dispatch(login(values))
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toast({
        description: `Welcome back, ${result.payload.displayName}!`,
      })
      navigate('/photos')
    } catch (err: any) {
      toast({
        title: 'Oops!',
        description: `${err.message}`,
        variant: 'destructive',
      })
    }
  }

  return (
    <div className="flex items-center justify-center min-h-screen">
      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(onSubmit)}
          className="w-4/12 p-8 space-y-4 border rounded-lg shadow-lg"
        >
          <div className="text-3xl text-center text-foreground">
            Welcome back!
          </div>
          <div className="text-center text-muted-foreground">
            Please login to your account.
          </div>
          <FormField
            control={form.control}
            name="email"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                  <Input placeholder="Email" {...field} />
                </FormControl>
                <FormDescription>We'll never share your email.</FormDescription>
                <FormMessage />
              </FormItem>
            )}
          />
          <FormField
            control={form.control}
            name="password"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                  <Input type="password" placeholder="Password" {...field} />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />
          <p>
            You don't have an account?{' '}
            <Link to="/register" className="text-primary hover:underline">
              Register
            </Link>
          </p>

          <Button
            className={
              form.formState.isSubmitting
                ? 'bg-muted cursor-loading hover:bg-muted'
                : 'bg:primary hover:bg-opacity-60'
            }
            type="submit"
          >
            {form.formState.isSubmitting ? 'Loading...' : 'Login'}
          </Button>
        </form>
      </Form>
    </div>
  )
}
