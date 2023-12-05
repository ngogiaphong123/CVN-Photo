import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import * as z from 'zod'

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
import { AppDispatch } from '@redux/store'
import { useDispatch } from 'react-redux'
import { register } from '@redux/slices/user.slice'
import { useToast } from '@components/ui/use-toast'

const formSchema = z
  .object({
    email: z.string().email({
      message: 'Please enter a valid email.',
    }),
    password: z
      .string()
      .min(8, {
        message: 'Password must be at least 8 characters',
      })
      .max(32, {
        message: 'Password must be at most 32 characters',
      }),
    confirmPassword: z
      .string()
      .min(8, {
        message: 'Password must be at least 8 characters',
      })
      .max(32, {
        message: 'Password must be at most 32 characters',
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
  .refine(data => data.password === data.confirmPassword, {
    message: 'Passwords do not match.',
    path: ['confirmPassword'],
  })

export function Register() {
  const dispatch = useDispatch<AppDispatch>()
  const { toast } = useToast()
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
      confirmPassword: '',
      displayName: '',
    },
  })

  const onSubmit = async (values: z.infer<typeof formSchema>) => {
    const result = await dispatch(register(values))
    try {
      if (result.meta.requestStatus === 'rejected')
        throw new Error(result.payload)
      toast({
        description: `Welcome, ${result.payload.displayName}!`,
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
    <div className="flex items-center justify-center min-h-screen">
      <Form {...form}>
        <form
          onSubmit={form.handleSubmit(onSubmit)}
          className="w-4/12 p-8 space-y-4 border rounded-lg shadow-lg"
        >
          <div className="text-3xl text-center text-foreground">Hi there!</div>
          <div className="text-center text-muted-foreground">
            Create your account to access all the lingering memories.
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
            name="displayName"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Display Name</FormLabel>
                <FormControl>
                  <Input placeholder="Display Name" {...field} />
                </FormControl>
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
          <FormField
            control={form.control}
            name="confirmPassword"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Confirm Password</FormLabel>
                <FormControl>
                  <Input
                    type="password"
                    placeholder="Confirm password"
                    {...field}
                  />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />

          <p>
            Already have an account?{' '}
            <Link to="/login" className="text-primary hover:underline">
              Login
            </Link>
          </p>
          <Button
            className={
              form.formState.isSubmitting
                ? 'bg-muted cursor-loading hover:bg-muted'
                : 'bg-primary hover:bg-opacity-60 text-white'
            }
            type="submit"
          >
            {form.formState.isSubmitting ? 'Loading...' : 'Register'}
          </Button>
        </form>
      </Form>
    </div>
  )
}
